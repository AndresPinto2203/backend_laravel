<?php

namespace App\Domain\ProductPrice\Repositories;

use Illuminate\Support\Collection;

interface ProductPriceRepository
{
    public function forProduct(int $productId): Collection;

    public function createForProduct(int $productId, array $data): array;
}