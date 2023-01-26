<?php

declare(strict_types=1);

namespace Acme\Commission\Infrastructure;

use Acme\Commission\Domain\CommissionInterface;
use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\Operation;

final class DepositCommission implements CommissionInterface
{
    public const COMMISSION = 0.0003;

    public function calculate(Operation $operation): Money
    {
        $amount = $operation->getAmount();

        return $amount
            ->multiplyBy(self::COMMISSION)
            ->toMajor()
            ->roundUp($amount->getDecimal());
    }

    public function supports(Operation $operation): bool
    {
        return $operation->getType() === Operation::TYPE_DEPOSIT;
    }
}
