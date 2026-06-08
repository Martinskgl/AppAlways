<?php

namespace App\Enums;

enum StatusGateway: string
{
    case APPROVED = 'approved';
    case DECLINED = 'declined';
}