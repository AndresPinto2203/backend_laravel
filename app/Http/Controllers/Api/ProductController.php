<?php

namespace App\Http\Controllers\Api;

use App\Application\Product\DTOs\ProductData;
use App\Application\Product\UseCases\CreateProduct;
use App\Application\Product\UseCases\DeleteProduct;
use App\Application\Product\UseCases\GetProductById;
use App\Application\Product\UseCases\GetProductList;
use App\Application\Product\UseCases\UpdateProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    public function __construct(
        private GetProductList $getProductList,
        private GetProductById $getProductById,
        private CreateProduct $createProduct,
        private UpdateProduct $updateProduct,
        private DeleteProduct $deleteProduct,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Obtener lista de productos",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de productos"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $paginator = $this->getProductList->execute($perPage);

        return ProductResource::collection($paginator);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = ProductData::fromArray($request->validated());

        try {
            $product = $this->createProduct->execute($data);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(new ProductResource((object) $product), 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->getProductById->execute($id);

        if (! $product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(new ProductResource((object) $product));
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        try {
            $product = $this->updateProduct->execute($id, $validated);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if (! $product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(new ProductResource((object) $product));
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->deleteProduct->execute($id);

        if (! $deleted) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(null, 204);
    }
}
