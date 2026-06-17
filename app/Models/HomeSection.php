<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [

        'title',

        'slug',

        'type',

        'category_id',

        'is_active',

        'sort_order'
    ];

    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MANUAL PRODUCTS
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->belongsToMany(

            Item::class,

            'home_section_items'

        );
    }
}