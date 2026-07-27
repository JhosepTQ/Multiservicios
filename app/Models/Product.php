<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'sku', 'price', 'cost', 'stock', 'min_stock', 'image_path', 'active'];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getMarginAttribute()
    {
        if ($this->cost == 0) return 0;
        return (($this->price - $this->cost) / $this->price) * 100;
    }
}
