<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'receipt_number', 'customer_name', 'customer_phone', 'customer_email', 'subtotal', 'tax', 'total', 'status', 'notes'];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function generateReceiptNumber()
    {
        $count = self::count();
        return 'BOL-' . date('Ymd') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
}
