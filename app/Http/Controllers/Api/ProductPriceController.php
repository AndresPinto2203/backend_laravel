<?php

namespace App\Http\Controllers\Api;

use App\Application\ProductPrice\UseCases\CreateProductPrice;
use App\Application\ProductPrice\UseCases\GetProductPrices;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class ProductPriceController extends Controller
{
    public function __construct(
        private GetProductPrices $getProductPrices,
        private CreateProductPrice $createProductPrice,
    ) {}

    public function index(int $productId): JsonResponse
    {
        $prices = $this->getProductPrices->execute($productId);

        return response()->json(ProductPriceResource::collection($prices));
    }

    public function store(StoreProductPriceRequest $request, int $productId): JsonResponse
    {
        try {
            $price = $this->createProductPrice->execute($productId, $request->validated());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json(new ProductPriceResource((object) $price), 201);
    }
}