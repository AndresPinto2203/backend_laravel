<?php

namespace App\Domain\Currency\Repositories;

interface CurrencyRepository
{
    public function exists(int $id): bool;
}