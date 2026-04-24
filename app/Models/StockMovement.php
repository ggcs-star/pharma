<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'item_id',
        'batch_id',

        'type',

        // 🔥 ADD THESE
        'sale_type',
        'movement_unit',

        'quantity',
        'running_stock',

        'reference_id',
        'reference_type',

        'user_id',
        'remarks'
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

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
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

    public function getDisplayMovementAttribute()
    {
        if ($this->sale_type === 'loose') {
            return abs($this->quantity) . ' Tablets';
        }

        return abs($this->quantity) . ' Strips';
    }

    public static function getBatchStock($batchId)
    {
        return self::where('batch_id', $batchId)->sum('quantity');
    }
}