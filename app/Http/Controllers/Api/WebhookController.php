<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;
use App\Http\Requests\WebhookRequest;
use App\Services\PaymentWebhookService;
use App\Http\Resources\PaymentResource;
use App\Models\Order;


class WebhookController extends Controller
{
    #[OA\Post(
        path: '/api/webhook/payment',
        summary: 'Confirmação de pagamento via webhook',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['order_id', 'status'],
                properties: [
                    new OA\Property(property: 'order_id', type: 'integer', example: 1),
                    new OA\Property(property: 'status', type: 'string', enum: ['approved', 'declined'], example: 'approved'),
                ]
            )
        ),
        tags: ['Webhook'],
        responses: [
            new OA\Response(response: 200, description: 'Webhook processado com sucesso'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function __construct(
        private readonly PaymentWebhookService $paymentWebhookService,
    ) {}

    public function __invoke(WebhookRequest $request)
    {        
        $order = Order::find($request->order_id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido não encontrado.',
            ], 404);
        }

        $this->paymentWebhookService->paymentProcess($order, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Pagamento processado com sucesso.',
            'data'    => new PaymentResource($order->refresh()),
        ]);

    }
}
