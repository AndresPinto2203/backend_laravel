<?php

namespace App\Application\Product\UseCases;

use App\Domain\Currency\Repositories\CurrencyRepository;
use App\Domain\Product\Repositories\ProductRepository;
use InvalidArgumentException;

class UpdateProduct
{
    public function __construct(
        private ProductRepository $products,
        private CurrencyRepository $currencies,
    ) {}

    public function execute(int $id, array $data): ?array
    {
        // Si viene currency_id, validamos que exista
        if (isset($data['currency_id']) && ! $this->currencies->exists((int) $data['currency_id'])) {
            throw new InvalidArgumentException('Currency not found');
        }

        return $this->products->update($id, $data);
    }
}