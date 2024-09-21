<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'price',
    ];

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    public function menus()
    {
        return $this->belongsTo(Menu::class);
    }
}
