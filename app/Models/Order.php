<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
 

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_date',
        'total_price',
        'customer_id',
        'discount',
        'discount_amount',
        'total_payment',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            $order->order_date ??= now();
            $order->total_price ??= 0;
        });

        static::updated(function (self $order) {
            $originalStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            if ($originalStatus === $newStatus) {
                return;
            }

            DB::transaction(function () use ($order, $originalStatus, $newStatus) {
                $order->loadMissing('orderdetails.product');

                if ($newStatus === 'cancelled' && $originalStatus !== 'cancelled') {
                    foreach ($order->orderdetails as $item) {
                        $product = $item->product;
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                }

                if ($originalStatus === 'cancelled' && in_array($newStatus, ['new', 'processing', 'completed'])) {
                    foreach ($order->orderdetails as $item) {
                        $product = $item->product;
                        if ($product) {
                            // Cek stok jika kembali aktif
                            if ($product->stock < $item->quantity) {
                                throw new \Exception("Stok {$product->name} tidak mencukupi untuk mengaktifkan kembali pesanan.");
                            }
                            $product->decrement('stock', $item->quantity);
                        }
                    }
                }
            });
        });
    }

    /**
     * Definisikan relasi ke model Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Definisikan relasi ke model OrderDetail
     */
    public function orderdetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}