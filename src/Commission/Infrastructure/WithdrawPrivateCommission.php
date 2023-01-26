<?php

declare(strict_types=1);

namespace Acme\Commission\Infrastructure;

use Acme\Commission\Domain\CommissionInterface;
use Acme\Commission\Domain\CurrencyConverterInterface;
use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\Operation;
use Acme\Commission\Domain\User;
use Acme\Shared\Date\Calendar;

final class WithdrawPrivateCommission implements CommissionInterface
{
    public const WEEKLY_DISCOUNT = 100000; // in cents
    public const WEEKLY_DISCOUNT_TIMES = 3;
    public const COMMISSION = 0.003;

    private array $discounts = [];

    private CurrencyConverterInterface $converter;

    public function __construct(CurrencyConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    public function calculate(Operation $operation): Money
    {
        $amount = $operation->getAmount();
        $currency = $amount->getCurrency();
        $discount = $this->createDiscount($operation);
        /** @var Money $discountLeft */
        $discountLeft = $discount->offsetGet('amount');

        if ($discountLeft->getAmount() > 0 && $discount->offsetGet('times') > 0) {
            $least = $this->converter->convert($amount, Money::EUR)->least($discountLeft);
            $discount['amount'] = $discountLeft->sub($least);
            --$discount['times'];

            $least = $this->converter->convertBack($least, $currency);

            return $amount
                ->diff($least)
                ->multiplyBy(self::COMMISSION)
                ->toMajor()
                ->roundUp($amount->getDecimal());
        }

        return $amount
            ->multiplyBy(self::COMMISSION)
            ->toMajor()
            ->roundUp($amount->getDecimal());
    }

    private function createDiscount(Operation $operation): \ArrayObject
    {
        $key = $this->discountKey($operation);

        if (array_key_exists($key, $this->discounts)) {
            return $this->discounts[$key];
        }

        return $this->discounts[$key] = new \ArrayObject([
            'amount' => Money::ofMinor(self::WEEKLY_DISCOUNT, Money::EUR),
            'times' => self::WEEKLY_DISCOUNT_TIMES,
        ]);
    }

    private function discountKey(Operation $operation): string
    {
        return sprintf(
            '%s-%s-%s',
            $operation->getUser()->getId(),
            Calendar::calculateYearAccordingToWeekNumberIntersection($operation->getDate()),
            $operation->getDate()->format('W'),
        );
    }

    public function supports(Operation $operation): bool
    {
        return $operation->getType() === Operation::TYPE_WITHDRAW
            && $operation->getUser()->getType() === User::TYPE_PRIVATE;
    }
}
