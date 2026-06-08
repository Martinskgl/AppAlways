<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\OrderController;

Route::post('/checkout', App\Http\Controllers\Api\CheckoutController::class);

Route::post('/webhook/payment', WebhookController::class);

Route::get('/orders/{order_id}', OrderController::class);
?>
