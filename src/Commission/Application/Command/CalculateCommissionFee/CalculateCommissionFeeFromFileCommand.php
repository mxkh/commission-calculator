<?php

declare(strict_types=1);

namespace Acme\Commission\Application\Command\CalculateCommissionFee;

final class CalculateCommissionFeeFromFileCommand
{
    public function __construct(
        readonly string $file,
        readonly string $exchangeRateUri,
    ) {
    }
}
