<?php

declare(strict_types=1);

namespace Acme\Tests\Commission\Domain;

use PHPUnit\Framework\TestCase;
use Acme\Commission\Domain\User;
use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\Operation;
use Webmozart\Assert\InvalidArgumentException;
use Acme\Commission\Domain\CommissionInterface;
use Acme\Commission\Infrastructure\DepositCommission;
use Acme\Commission\Domain\CommissionFeeCalculatorContext;

final class CommissionFeeCalculatorContextTest extends TestCase
{
    public function testCreationWithWronStrategyInstance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected an instance of Acme\Commission\Domain\CommissionInterface. Got: stdClass',
        );

        new CommissionFeeCalculatorContext([new \stdClass()]);
    }

    /** @dataProvider provideOperation() */
    public function testNotSupportedStrategy(Operation $operation): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Commission strategy was not found');

        $notSupportedStrategy = $this->createMock(CommissionInterface::class);
        $notSupportedStrategy->method('supports')->willReturn(false);

        $context = new CommissionFeeCalculatorContext([$notSupportedStrategy]);
        $context->execute($operation);
    }

    /** @dataProvider provideOperation() */
    public function testSupportedStrategy(Operation $operation): void
    {
        $context = new CommissionFeeCalculatorContext([new DepositCommission()]);
        $strategy = $context->execute($operation);
        $this->assertInstanceOf(DepositCommission::class, $strategy);
    }

    public function provideOperation(): iterable
    {
        yield [
            new Operation(
                new \DateTimeImmutable('now'),
                new User(1, User::TYPE_BUSINESS),
                Operation::TYPE_DEPOSIT,
                Money::ofMinor(100, Money::EUR)
            ),
        ];
    }
}
