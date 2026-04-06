<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'sv23810310083_categories';

    protected $fillable = ['name', 'slug', 'description', 'is_visible'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}