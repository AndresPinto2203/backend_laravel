<?php

namespace App\Application\Product\DTOs;

class ProductData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $currencyId,
        public float $taxCost,
        public float $manufacturingCost,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['description'] ?? null,
            (float) $data['price'],
            (int) $data['currency_id'],
            (float) $data['tax_cost'],
            (float) $data['manufacturing_cost'],
        );
    }

    public function toArray(): array
    {
        return [
            'name'               => $this->name,
            'description'        => $this->description,
            'price'              => $this->price,
            'currency_id'        => $this->currencyId,
            'tax_cost'           => $this->taxCost,
            'manufacturing_cost' => $this->manufacturingCost,
        ];
    }
}