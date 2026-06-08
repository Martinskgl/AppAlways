<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'AppAlways API',
    version: '1.0.0',
    description: 'API de Checkout - Teste Técnico AlwaysFit'
)]
#[OA\Server(
    url: '/',
    description: 'Local'
)]


abstract class Controller
{
    //
}
