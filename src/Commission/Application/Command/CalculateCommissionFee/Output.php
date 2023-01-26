<?php

declare(strict_types=1);

namespace Acme\Commission\Application\Command\CalculateCommissionFee;

use Acme\Commission\Domain\Money;

final class Output implements \Stringable
{
    public int|float|string $commission;

    public string $currency;

    public function __construct(Money $commission)
    {
        $this->commission = $commission->getAmount();
        $this->currency = $commission->getCurrency();
    }

    public function __toString(): string
    {
        $formatter = new \NumberFormatter('en-US', \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($this->commission, $this->currency);
    }
}
