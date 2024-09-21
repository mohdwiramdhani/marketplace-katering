<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Menu extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'merchant_id',
        'name',
        'quantity',
        'price',
        'description',
        'photo',
        'type'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'menu_id');
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
