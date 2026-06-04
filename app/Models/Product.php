<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'price', 'purchase_price', 'stock', 'image', 'is_active'];

    protected $appends = ['image_url', 'profit'];
    protected $casts = ['is_active' => 'boolean', 'price' => 'integer', 'purchase_price' => 'integer'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? '/storage/' . $this->image : null);
    }

    public function profit(): Attribute
    {
        return Attribute::get(fn () => $this->price - $this->purchase_price);
    }
}
