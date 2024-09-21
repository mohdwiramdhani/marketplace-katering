<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'menu_id',
        'quantity',
        'total_price',
        'address',
        'contact',
        'notes',
        'status',
        "payment_receipt",
        "delivery_date",
        "company_name"
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
