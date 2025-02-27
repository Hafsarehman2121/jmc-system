<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'productType',
        'brand',
        'numberOfContainers',
        'weightOfBag',
       
    ];

    protected $casts = [
        'weightOfBag' => 'array', // Cast JSON to array
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
