<?php

declare(strict_types=1);

namespace Acme\Commission\Domain;

interface CurrencyConverterInterface
{
    public function convert(Money $from, string $to): Money;

    public function convertBack(Money $from, string $to): Money;
}
