<?php

namespace App\Enums;

enum StatusOrder: string
{
    case paid = 'paid';
    case failed = 'failed';
    case pending = 'pending';
}