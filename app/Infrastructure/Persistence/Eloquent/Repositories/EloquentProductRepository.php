<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Product\Repositories\ProductRepository;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ProductModel::with('currency')->paginate($perPage);
    }

    public function find(int $id): ?array
    {
        $model = ProductModel::with('currency', 'prices.currency')->find($id);
        return $model?->toArray();
    }

    public function create(array $data): array
    {
        $model = ProductModel::create($data);
        return $model->fresh(['currency'])->toArray();
    }

    public function update(int $id, array $data): ?array
    {
        $model = ProductModel::find($id);

        if (! $model) {
            return null;
        }

        $model->update($data);

        return $model->fresh(['currency'])->toArray();
    }

    public function delete(int $id): bool
    {
        $model = ProductModel::find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }
}