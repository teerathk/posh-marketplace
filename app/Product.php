<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected   $table          = 'products';
    protected   $primaryKey     = 'id';
    public      $timestamps     = true;

    protected $fillable = [
        'images', 'name', 'brand', 'sku', 'description', 'vendor_id', 'net_price',
        'sale_price', 'created_at', 'updated_at', 'featured_image', 'parent_category', 'sub_category'
    ];
}
