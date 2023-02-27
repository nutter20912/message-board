<?php

namespace App\Services\Logging;

/**
 * 日誌種類
 */
enum LoggingType: string
{
    case REQUEST = 'request';
    case QUERY = 'query';
}
