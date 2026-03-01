<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: 'Products API',
        version: '1.0.0',
        description: 'API de gestión de productos'
    ),
    components: new OA\Components(
        securitySchemes: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'JWT'
            )
        ]
    ),
    paths: [
        // Auth: login para obtener token JWT
        new OA\PathItem(
            path: '/api/login',
            post: new OA\Post(
                operationId: 'login',
                tags: ['Auth'],
                summary: 'Iniciar sesión y obtener token JWT',
                requestBody: new OA\RequestBody(
                    required: true,
                    content: new OA\JsonContent(
                        required: ['email', 'password'],
                        properties: [
                            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@test.com'),
                            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'admin_123456'),
                        ]
                    )
                ),
                responses: [
                    new OA\Response(
                        response: 200,
                        description: 'Login exitoso, retorna token JWT',
                        content: new OA\JsonContent(
                            properties: [
                                new OA\Property(property: 'access_token', type: 'string'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                                new OA\Property(property: 'expires_in', type: 'integer'),
                            ]
                        )
                    ),
                    new OA\Response(
                        response: 401,
                        description: 'Credenciales inválidas'
                    )
                ]
            )
        ),

        // Products: listado y creación
        new OA\PathItem(
            path: '/api/products',
            get: new OA\Get(
                operationId: 'getProducts',
                tags: ['Products'],
                summary: 'Obtener lista de productos',
                security: [ ['bearerAuth' => []] ],
                parameters: [
                    new OA\Parameter(
                        name: 'per_page',
                        description: 'Elementos por página',
                        in: 'query',
                        required: false,
                        schema: new OA\Schema(type: 'integer', default: 15)
                    )
                ],
                responses: [
                    new OA\Response(
                        response: 200,
                        description: 'Lista paginada de productos'
                    ),
                    new OA\Response(
                        response: 401,
                        description: 'No autenticado'
                    )
                ]
            ),
            post: new OA\Post(
                operationId: 'createProduct',
                tags: ['Products'],
                summary: 'Crear un nuevo producto',
                security: [ ['bearerAuth' => []] ],
                requestBody: new OA\RequestBody(
                    required: true,
                    content: new OA\JsonContent(
                        required: ['name', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost'],
                        properties: [
                            new OA\Property(property: 'name', type: 'string', example: 'Producto prueba'),
                            new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Descripción de prueba'),
                            new OA\Property(property: 'price', type: 'number', format: 'float', example: 10.5),
                            new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                            new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 2.5),
                            new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 3.0),
                        ]
                    )
                ),
                responses: [
                    new OA\Response(
                        response: 201,
                        description: 'Producto creado correctamente'
                    ),
                    new OA\Response(
                        response: 422,
                        description: 'Datos inválidos'
                    ),
                    new OA\Response(
                        response: 401,
                        description: 'No autenticado'
                    )
                ]
            )
        ),

        // Products: operaciones sobre un producto concreto
        new OA\PathItem(
            path: '/api/products/{id}',
            get: new OA\Get(
                operationId: 'getProductById',
                tags: ['Products'],
                summary: 'Obtener un producto por ID',
                security: [ ['bearerAuth' => []] ],
                parameters: [
                    new OA\Parameter(
                        name: 'id',
                        in: 'path',
                        required: true,
                        description: 'ID del producto',
                        schema: new OA\Schema(type: 'integer', example: 1)
                    )
                ],
                responses: [
                    new OA\Response(
                        response: 200,
                        description: 'Producto encontrado'
                    ),
                    new OA\Response(
                        response: 404,
                        description: 'Producto no encontrado'
                    ),
                    new OA\Response(
                        response: 401,
                        description: 'No autenticado'
                    )
                ]
            ),
            put: new OA\Put(
                operationId: 'updateProduct',
                tags: ['Products'],
                summary: 'Actualizar un producto',
                security: [ ['bearerAuth' => []] ],
                parameters: [
                    new OA\Parameter(
                        name: 'id',
                        in: 'path',
                        required: true,
                        description: 'ID del producto',
                        schema: new OA\Schema(type: 'integer', example: 1)
                    )
                ],
                requestBody: new OA\RequestBody(
                    required: true,
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(property: 'name', type: 'string', example: 'Producto actualizado'),
                            new OA\Property(property: 'description', type: 'string', nullable: true),
                            new OA\Property(property: 'price', type: 'number', format: 'float'),
                            new OA\Property(property: 'currency_id', type: 'integer'),
                            new OA\Property(property: 'tax_cost', type: 'number', format: 'float'),
                            new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float'),
                        ]
                    )
                ),
                responses: [
                    new OA\Response(
                        response: 200,
                        description: 'Producto actualizado correctamente'
                    ),
                    new OA\Response(
                        response: 404,
                        description: 'Producto no encontrado'
                    ),
                    new OA\Response(
                        response: 401,
                        description: 'No autenticado'
                    )
                ]
            ),
            delete: new OA\Delete(
                operationId: 'deleteProduct',
                tags: ['Products'],
                summary: 'Eliminar un producto',
                security: [ ['bearerAuth' => []] ],
                parameters: [
                    new OA\Parameter(
                        name: 'id',
                        in: 'path',
                        required: true,
                        description: 'ID del producto',
                        schema: new OA\Schema(type: 'integer', example: 1)
                    )
                ],
                responses: [
                    new OA\Response(
                        response: 204,
                        description: 'Producto eliminado correctamente'
                    ),
                    new OA\Response(
                        response: 404,
                        description: 'Producto no encontrado'
                    ),
                    new OA\Response(
                        response: 401,
                        description: 'No autenticado'
                    )
                ]
            )
        ),

        // Product prices: listado y creación de precios en otras divisas
        new OA\PathItem(
            path: '/api/products/{id}/prices',
            get: new OA\Get(
                operationId: 'getProductPrices',
                tags: ['Product Prices'],
                summary: 'Obtener lista de precios de un producto',
                security: [ ['bearerAuth' => []] ],
                parameters: [
                    new OA\Parameter(
                        name: 'id',
                        in: 'path',
                        required: true,
                        description: 'ID del producto',
                        schema: new OA\Schema(type: 'integer', example: 1)
                    )
                ],
                responses: [
                    new OA\Response(
                        response: 200,
                        description: 'Lista de precios del producto'
                    ),
                    new OA\Response(
                        response: 404,
                        description: 'Producto no encontrado'
                    ),
                    new OA\Response(
                        response: 401,
                        description: 'No autenticado'
                    )
                ]
            ),
            post: new OA\Post(
                operationId: 'createProductPrice',
                tags: ['Product Prices'],
                summary: 'Crear un nuevo precio para un producto',
                security: [ ['bearerAuth' => []] ],
                parameters: [
                    new OA\Parameter(
                        name: 'id',
                        in: 'path',
                        required: true,
                        description: 'ID del producto',
                        schema: new OA\Schema(type: 'integer', example: 1)
                    )
                ],
                requestBody: new OA\RequestBody(
                    required: true,
                    content: new OA\JsonContent(
                        required: ['currency_id', 'price'],
                        properties: [
                            new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                            new OA\Property(property: 'price', type: 'number', format: 'float', example: 9.99),
                        ]
                    )
                ),
                responses: [
                    new OA\Response(
                        response: 201,
                        description: 'Precio creado correctamente'
                    ),
                    new OA\Response(
                        response: 404,
                        description: 'Producto no encontrado'
                    ),
                    new OA\Response(
                        response: 401,
                        description: 'No autenticado'
                    )
                ]
            )
        ),
    ]
)]
class OpenApiAttributes
{
}
