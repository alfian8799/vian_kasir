<?php

namespace App\Models;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderDetail extends Model
{
    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'subtotal',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $orderDetail) {
            $orderDetail->subtotal ??= 0;

            if (! $orderDetail->order_id) {
                return;
            }

            $order = $orderDetail->order()->first();
            if (! $order || ! in_array($order->status, ['new', 'processing', 'completed'])) {
                return;
            }

            $product = Product::query()
                ->lockForUpdate()
                ->find($orderDetail->product_id);

            if (! $product) {
                throw ValidationException::withMessages([
                    'order_details' => 'Product tidak ditemukan.',
                ]);
            }

            if ($orderDetail->quantity < 1 || $orderDetail->quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'order_details' => "Stok {$product->name} tidak mencukupi. Stok tersedia: {$product->stock}.",
                ]);
            }

            $product->decrement('stock', $orderDetail->quantity);
        });

        static::updating(function (self $orderDetail) {
            $originalQuantity = $orderDetail->getOriginal('quantity');
            $newQuantity = $orderDetail->quantity;

            if ($originalQuantity === $newQuantity) {
                return;
            }

            $order = $orderDetail->order()->first();
            if (! $order || ! in_array($order->status, ['new', 'processing', 'completed'])) {
                return;
            }

            $product = Product::query()
                ->lockForUpdate()
                ->find($orderDetail->product_id);

            if (! $product) {
                throw ValidationException::withMessages([
                    'order_details' => 'Product tidak ditemukan.',
                ]);
            }

            $quantityDiff = $newQuantity - $originalQuantity;

            if ($quantityDiff > 0) {
                if ($quantityDiff > $product->stock) {
                    throw ValidationException::withMessages([
                        'order_details' => "Stok {$product->name} tidak mencukupi untuk penambahan. Stok tersedia: {$product->stock}.",
                    ]);
                }
                $product->decrement('stock', $quantityDiff);
            } elseif ($quantityDiff < 0) {
                $product->increment('stock', abs($quantityDiff));
            }
        });

        static::deleting(function (self $orderDetail) {
            $order = $orderDetail->order()->first();
            if (! $order || ! in_array($order->status, ['new', 'processing', 'completed'])) {
                return;
            }

            $product = Product::query()
                ->lockForUpdate()
                ->find($orderDetail->product_id);

            if ($product) {
                $product->increment('stock', $orderDetail->quantity);
            }
        });
    }

    /**
     * Definisikan relasi ke model Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Definisikan relasi ke model Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Definisikan relasi ke model Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
