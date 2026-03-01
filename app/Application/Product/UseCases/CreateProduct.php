<?php

namespace App\Application\Product\UseCases;

use App\Application\Product\DTOs\ProductData;
use App\Domain\Currency\Repositories\CurrencyRepository;
use App\Domain\Product\Repositories\ProductRepository;
use InvalidArgumentException;

class CreateProduct
{
    public function __construct(
        private ProductRepository $products,
        private CurrencyRepository $currencies,
    ) {}

    public function execute(ProductData $data): array
    {
        if (! $this->currencies->exists($data->currencyId)) {
            throw new InvalidArgumentException('Currency not found');
        }

        return $this->products->create($data->toArray());
    }
}