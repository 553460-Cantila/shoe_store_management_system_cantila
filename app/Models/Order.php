<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'customer_name',
        'shoe_product_id', 
        'user_id',
        'quantity',
        'unit_price',
        'total_price',
        'paid_amount',
        'order_status',
        'payment_status',
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function shoeProduct(): BelongsTo
    {
        return $this->belongsTo(ShoeProduct::class, 'shoe_product_id'); 
    }

    public function menu()
    {
        return $this->shoeProduct();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total_price - $this->paid_amount);
    }

    public function updatePaymentStatus(): void
    {
        if ($this->paid_amount >= $this->total_price) {
            $this->payment_status = 'paid';
            $this->order_status = 'completed';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partial';
            $this->order_status = 'processing';
        } else {
            $this->payment_status = 'unpaid';
            $this->order_status = 'pending';
        }
        $this->save();
    }
}