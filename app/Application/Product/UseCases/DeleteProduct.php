<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Repositories\ProductRepository;

class DeleteProduct
{
    public function __construct(
        private ProductRepository $products,
    ) {}

    public function execute(int $id): bool
    {
        return $this->products->delete($id);
    }
}