<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductModel extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'tax_cost',
        'manufacturing_cost',
    ];

    protected $casts = [
        'price'              => 'float',
        'tax_cost'           => 'float',
        'manufacturing_cost' => 'float',
        'currency_id'        => 'integer',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(CurrencyModel::class, 'currency_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPriceModel::class, 'product_id');
    }
}