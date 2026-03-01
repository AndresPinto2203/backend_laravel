<?php

namespace App\Domain\Product\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?array;

    public function create(array $data): array;

    public function update(int $id, array $data): ?array;

    public function delete(int $id): bool;
}