<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $fillable = [
        'name',
        'price',
        'stock',
        'image',
    ];

    /**
     * Definisikan relasi ke model Order
     */
    public function orderdetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

}

