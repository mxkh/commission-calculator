<?php

declare(strict_types=1);

namespace Acme\Commission\Domain;

use Webmozart\Assert\Assert;

final class Money
{
    public const USD = 'USD';
    public const EUR = 'EUR';
    public const JPY = 'JPY';

    private int|float|string $amount;

    private string $currency;

    public function __construct(int|float|string $amount, string $currency)
    {
        Assert::inArray($currency, $this->getAvailableCurrencies());

        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAvailableCurrencies(): array
    {
        return [self::EUR, self::USD, self::JPY];
    }

    public static function parseCurrencyFromString(string $number, string $currency): self
    {
        return new self($number, $currency);
    }

    public static function ofMinor(int $minor, string $currency): self
    {
        return new self($minor, $currency);
    }

    public function multiplyBy(int|float $that): self
    {
        $amount = $this->amount * $that;

        return new self($amount, $this->currency);
    }

    public function toMajor(): self
    {
        $amount = $this->amount / 100;

        return new Money($amount, $this->currency);
    }

    public function toMinor(): self
    {
        $amount = $this->amount * 100;

        return new Money((int) $amount, $this->currency);
    }

    public function roundUp(int $precision = 0): self
    {
        $pow = 10 ** $precision;
        $amount = ceil($this->amount * $pow) / $pow;

        return new Money($amount, $this->currency);
    }

    public function diff(Money $money): self
    {
        $amount = abs($this->getAmount() - $money->getAmount());

        return new self($amount, $this->currency);
    }

    public function getAmount(): int|float|string
    {
        return $this->amount;
    }

    public function least(Money $money): self
    {
        $left = $this->getAmount();
        $right = $money->getAmount();

        $least = $left <= $right ? 0 : 1;

        return $least === 0 ? $this : $money;
    }

    public function sub(Money $money): self
    {
        $amount = $this->getAmount() - $money->getAmount();

        return new self($amount, $this->currency);
    }

    public function getDecimal(): int
    {
        return [self::JPY => 0, self::USD => 2, self::EUR => 2][$this->getCurrency()];
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
