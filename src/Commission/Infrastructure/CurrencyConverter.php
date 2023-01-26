<?php

declare(strict_types=1);

namespace Acme\Commission\Infrastructure;

use Acme\Commission\Domain\CurrencyConverterInterface;
use Acme\Commission\Domain\Money;
use Acme\Shared\Currency\Rate\Provider\ProviderInterface;

final class CurrencyConverter implements CurrencyConverterInterface
{
    private ProviderInterface $provider;

    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function convert(Money $from, string $to): Money
    {
        $rate = $this->provider->getRateByCode($from->getCurrency());
        $amount = $from->getAmount() / $rate;

        return new Money($amount, $to);
    }

    public function convertBack(Money $from, string $to): Money
    {
        $rate = $this->provider->getRateByCode($to);
        $amount = $from->getAmount() * $rate;

        return new Money($amount, $to);
    }
}
