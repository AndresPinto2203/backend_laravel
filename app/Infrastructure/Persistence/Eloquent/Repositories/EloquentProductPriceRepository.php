<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\ProductPrice\Repositories\ProductPriceRepository;
use App\Infrastructure\Persistence\Eloquent\Models\ProductPriceModel;
use Illuminate\Support\Collection;

class EloquentProductPriceRepository implements ProductPriceRepository
{
    public function forProduct(int $productId): Collection
    {
        return ProductPriceModel::with('currency')
            ->where('product_id', $productId)
            ->get();
    }

    public function createForProduct(int $productId, array $data): array
    {
        $data['product_id'] = $productId;

        $model = ProductPriceModel::create($data);

        return $model->fresh(['currency'])->toArray();
    }
}
