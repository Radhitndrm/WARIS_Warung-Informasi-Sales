<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'method', 'amount', 'change_amount', 'midtrans_id', 'status'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
