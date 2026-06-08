<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;
use App\Http\Resources\OrderResource;


class OrderController extends Controller
{
    #[OA\Get(
        path: '/api/orders/{order_id}',
        summary: 'Consultar pedido',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(
                name: 'order_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pedido encontrado'),
            new OA\Response(response: 404, description: 'Pedido não encontrado'),
        ]
    )]


    public function __invoke(string $order_id)
    {
        $order = Order::with(['customer', 'orderItems.product'])
            ->find($order_id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido não encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pedido encontrado.',
            'data' => new OrderResource($order),
        ], 200);
    }
}
