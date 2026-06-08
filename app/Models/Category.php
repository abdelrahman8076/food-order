<?php

namespace App\Models;

use App\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasLocalizedAttributes;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(Item::class)->orderBy('sort_order');
    }

    public function activeItems()
    {
        return $this->hasMany(Item::class)->where('is_available', true)->orderBy('sort_order');
    }
}
