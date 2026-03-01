<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Currency\Repositories\CurrencyRepository;
use App\Infrastructure\Persistence\Eloquent\Models\CurrencyModel;

class EloquentCurrencyRepository implements CurrencyRepository
{
    public function exists(int $id): bool
    {
        return CurrencyModel::whereKey($id)->exists();
    }
}