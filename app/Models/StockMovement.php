<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    protected $fillable = [
        'item_id',
        'batch_id',

        'type',

        // strip / loose
        'sale_type',
        'movement_unit',

        // stock direction
        'direction',

        // stock values
        'quantity',
        'running_stock',

        // source reference
        'reference_id',
        'reference_type',

        // audit
        'user_id',
        'remarks',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'quantity' => 'decimal:2',
        'running_stock' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isLooseSale()
    {
        return $this->sale_type === 'loose';
    }

    public function isStripSale()
    {
        return $this->sale_type === 'strip';
    }

    public function isStockIn()
    {
        return $this->direction === 'in';
    }

    public function isStockOut()
    {
        return $this->direction === 'out';
    }

    public function getDisplayMovementAttribute()
    {
        if ($this->sale_type === 'loose') {
            return abs($this->quantity) . ' Tablets';
        }

        return abs($this->quantity) . ' Strips';
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helper
    |--------------------------------------------------------------------------
    */

    public static function getBatchStock($batchId)
    {
        return self::where('batch_id', $batchId)
            ->latest()
            ->value('running_stock') ?? 0;
    }
}