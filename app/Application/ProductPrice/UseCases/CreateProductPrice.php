<?php

namespace App\Application\ProductPrice\UseCases;

use App\Domain\Currency\Repositories\CurrencyRepository;
use App\Domain\Product\Repositories\ProductRepository;
use App\Domain\ProductPrice\Repositories\ProductPriceRepository;
use InvalidArgumentException;

class CreateProductPrice
{
    public function __construct(
        private ProductPriceRepository $prices,
        private ProductRepository $products,
        private CurrencyRepository $currencies,
    ) {}

    public function execute(int $productId, array $data): array
    {
        // Comprobar que producto y divisa existen
        if (! $this->products->find($productId)) {
            throw new InvalidArgumentException('Product not found');
        }

        if (! $this->currencies->exists((int) $data['currency_id'])) {
            throw new InvalidArgumentException('Currency not found');
        }

        return $this->prices->createForProduct($productId, $data);
    }
}