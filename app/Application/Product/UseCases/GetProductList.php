<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetProductList
{
    public function __construct(
        private ProductRepository $products,
    ) {}

    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return $this->products->paginate($perPage);
    }
}