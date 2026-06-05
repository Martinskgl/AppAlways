<?php

namespace App\Enums;

enum StatusOrder: string
{
    case PAID = 'paid';
    case FAILED = 'failed';
    case PENDING = 'pending';
}