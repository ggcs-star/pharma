<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'item_id',
        'batch_id',
        'type',
        'quantity',
        'running_stock',
        'reference_id',
        'reference_type',
        'user_id',
        'remarks'
    ];

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

    public static function getBatchStock($batchId)
    {
        return self::where('batch_id', $batchId)->sum('quantity');
    }
}