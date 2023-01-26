<?php

declare(strict_types=1);

namespace Acme\Commission\Infrastructure;

use Acme\Commission\Domain\CommissionInterface;
use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\Operation;
use Acme\Commission\Domain\User;

final class WithdrawBusinessCommission implements CommissionInterface
{
    public const COMMISSION = 0.005;

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
        return $operation->getType() === Operation::TYPE_WITHDRAW
            && $operation->getUser()->getType() === User::TYPE_BUSINESS;
    }
}
