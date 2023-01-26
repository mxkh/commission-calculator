<?php

declare(strict_types=1);

namespace Acme\Tests\Commission\Infrastructure;

use PHPUnit\Framework\TestCase;
use Acme\Commission\Domain\Money;
use Acme\Commission\Infrastructure\CurrencyConverter;
use Acme\Shared\Currency\Rate\Provider\ProviderInterface;

final class CurrencyConverterTest extends TestCase
{
    public function testConverter(): void
    {
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('getRateByCode')
            ->with('JPY')
            ->willReturn(130);

        $converter = new CurrencyConverter($provider);
        $money = $converter->convert(new Money(10000, 'JPY'), 'EUR');
        $this->assertEquals(76.92307692307692, $money->getAmount());
        $this->assertEquals('EUR', $money->getCurrency());

        $money = $converter->convertBack(new Money(76.92307692307692, 'EUR'), 'JPY');
        $this->assertEquals(10000, $money->getAmount());
        $this->assertEquals('JPY', $money->getCurrency());
    }
}
