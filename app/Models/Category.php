<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'parent_id',
     ];

     use HasFactory;

     // Relating Category to product
     public function products()
     {
        return $this->hasMany(Product::class);
     }
}
