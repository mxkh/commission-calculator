<?php

declare(strict_types=1);

namespace Acme\Tests\Commission\Domain;

use PHPUnit\Framework\TestCase;
use Acme\Commission\Domain\Money;
use Webmozart\Assert\InvalidArgumentException;

final class MoneyTest extends TestCase
{
    public function testCreation(): void
    {
        $money = new Money(1, Money::USD);
        $this->assertEquals(1, $money->getAmount());
        $this->assertEquals('USD', $money->getCurrency());
        Money::ofMinor(100, Money::USD);
    }

    public function testCreationWithNotSupportedCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Money(1, 'USS');
    }

    public function testParseCurrencyFromString(): void
    {
        $money = Money::parseCurrencyFromString('1', Money::USD);
        $this->assertEquals('1', $money->getAmount());
    }

    public function testToMinor(): void
    {
        $money = Money::parseCurrencyFromString('1', Money::USD)->toMinor();
        $this->assertEquals(100, $money->getAmount());
        $money = (new Money(1, Money::USD))->toMinor();
        $this->assertEquals(100, $money->getAmount());
    }

    public function testToMajor(): void
    {
        $money = Money::parseCurrencyFromString('100', Money::USD)->toMajor();
        $this->assertEquals(1, $money->getAmount());
        $money = (new Money(100, Money::USD))->toMajor();
        $this->assertEquals(1, $money->getAmount());
    }

    public function testDiff(): void
    {
        $money = (new Money(1000, Money::USD))->diff(new Money(1500, Money::USD));
        $this->assertEquals(500, $money->getAmount());
        $money = (new Money(100, Money::USD))->diff(new Money(100, Money::USD));
        $this->assertEquals(0, $money->getAmount());
    }

    public function testSub(): void
    {
        $money = (new Money(100, Money::USD))->sub(new Money(10, Money::USD));
        $this->assertEquals(90, $money->getAmount());
    }

    public function testMultiply(): void
    {
        $money = (new Money(2, Money::USD))->multiplyBy(2);
        $this->assertEquals(4, $money->getAmount());
    }

    public function testRoundUp(): void
    {
        $eur = new Money(0.023, Money::EUR);
        $eurAmount = $eur->roundUp($eur->getDecimal())->getAmount();
        $this->assertEquals(0.03, $eurAmount);

        $jpy = new Money(6547.43, Money::JPY);
        $jpyAmount = $jpy->roundUp($jpy->getDecimal())->getAmount();
        $this->assertEquals(6548, $jpyAmount);
    }

    public function testLeast(): void
    {
        $money = (new Money(1, Money::USD))->least(new Money(2, Money::USD));
        $this->assertEquals(1, $money->getAmount());
    }
}
