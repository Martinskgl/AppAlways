<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use OpenApi\Attributes as OA;
use App\Http\Resources\OrderResource;

class CheckoutController extends Controller
{

    #[OA\Post(
        path: '/api/checkout',
        summary: 'Realizar checkout',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['customer_id', 'items', 'credit_card'],
                properties: [
                    new OA\Property(property: 'customer_id', type: 'integer', example: 1),
                    new OA\Property(property: 'items', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'product_id', type: 'integer', example: 1),
                            new OA\Property(property: 'quantity', type: 'integer', example: 2),
                        ]
                    )),
                    new OA\Property(property: 'credit_card', type: 'object', properties: [
                        new OA\Property(property: 'holder_name', type: 'string', example: 'João Silva'),
                        new OA\Property(property: 'number', type: 'string', example: '1234567890123456'),
                        new OA\Property(property: 'expiry_month', type: 'integer', example: 12),
                        new OA\Property(property: 'expiry_year', type: 'integer', example: 2027),
                        new OA\Property(property: 'cvv', type: 'string', example: '123'),
                    ]),
                ]
            )
        ),
        tags: ['Checkout'],
        responses: [
            new OA\Response(response: 201, description: 'Pedido criado com sucesso'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]

    public function __construct(
        private readonly CheckoutService $checkoutService,
    ){}

    public function __invoke(CheckoutRequest $request)
    {
        $order = $this->checkoutService->process($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Checkout realizado com sucesso',
            'data' => new OrderResource($order),
        ], 201);
    }
}
