<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Repositories\ProductRepository;

class GetProductById
{
    public function __construct(
        private ProductRepository $products,
    ) {}

    public function execute(int $id): ?array
    {
        return $this->products->find($id);
    }
}