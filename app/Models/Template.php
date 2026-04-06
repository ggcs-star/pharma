<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['short_code', 'bounce_day'];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }
}