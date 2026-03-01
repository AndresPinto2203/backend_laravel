<?php

namespace App\Application\ProductPrice\UseCases;

use App\Domain\ProductPrice\Repositories\ProductPriceRepository;
use Illuminate\Support\Collection;

class GetProductPrices
{
    public function __construct(
        private ProductPriceRepository $prices,
    ) {}

    public function execute(int $productId): Collection
    {
        return $this->prices->forProduct($productId);
    }
}