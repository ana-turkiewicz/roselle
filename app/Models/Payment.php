<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'status',
        'amount',
        'transaction_id',
        'paid_at',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}