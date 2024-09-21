<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $table = 'merchants';

    protected $fillable = [
        'merchant_id',
        'company_name',
        'contact',
        'address',
        'description',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'merchant_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'merchant_id');
    }
}
