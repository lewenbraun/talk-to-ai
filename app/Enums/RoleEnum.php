<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: string
{
    case SYSTEM = 'system';
    case USER = 'user';
    case ASSISTANT = 'assistant';
    case TOOL = 'tool';
}
