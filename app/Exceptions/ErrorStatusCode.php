<?php

namespace App\Exceptions;

use Throwable;

enum ErrorStatusCode: int
{
    case BadRequest = 400;

    /**
     * 依實例取得 enum case
     *
     * @return self|null
     */
    public static function fromInstance(Throwable $exception): ?self
    {
        return match ($exception::class) {
            BadRequestException::class => self::BadRequest,
            default => null,
        };
    }
}
