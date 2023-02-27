<?php

namespace App\Services;

use App\Services\Logging\LoggingType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 日誌紀錄服務
 */
class LoggingService
{
    /**
     * 日誌紀錄
     *
     * @var array
     */
    protected static $records = [];

    /**
     * 加入日誌紀錄
     *
     * @param LoggingType $loggingType
     * @param array $record
     * @return void
     */
    public static function record(LoggingType $loggingType, $record)
    {
        static::$records[] = [
            'type' => $loggingType->value,
            'content' => $record
        ];
    }

    /**
     * 批量儲存日誌紀錄
     *
     * @return void
     */
    public static function batchStore()
    {
        $batchId = Str::orderedUuid()->toString();

        collect(static::$records)
            ->each(fn ($record) => Log::info($batchId, $record));
    }
}
