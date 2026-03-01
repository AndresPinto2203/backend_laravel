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
            )
        )
    ]
)]
class OpenApiAttributes
{
}
