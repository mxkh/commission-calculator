<?php

declare(strict_types=1);

namespace Acme\Shared\Filesystem\Reader;

final class CsvReaderResult
{
    public function __construct(
        public readonly string $date,
        public readonly int $userId,
        public readonly string $userType,
        public readonly string $type,
        public readonly string $amount,
        public readonly string $currency,
    ) {
    }
}
