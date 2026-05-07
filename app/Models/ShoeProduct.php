<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoeProduct extends Model
{
    use HasFactory;

    protected $table = 'shoe_products';

    protected $fillable = [
        'name',
        'category',
        'size',
        'color',
        'price',
        'stock',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shoe_product_id');
    }

    public function reduceStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    public function increaseStock(int $quantity): void
    {
        $this->stock += $quantity;
        $this->save();
    }
}