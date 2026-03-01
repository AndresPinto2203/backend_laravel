<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurrencyModel extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    protected $casts = [
        'exchange_rate' => 'float',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class, 'currency_id');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPriceModel::class, 'currency_id');
    }
}