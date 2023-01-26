<?php

declare(strict_types=1);

namespace Acme\Tests\Commission\Infrastructure;

use PHPUnit\Framework\TestCase;
use Acme\Commission\Domain\User;
use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\Operation;
use Acme\Commission\Infrastructure\DepositCommission;
use Acme\Commission\Domain\CurrencyConverterInterface;
use Acme\Commission\Infrastructure\WithdrawPrivateCommission;
use Acme\Commission\Infrastructure\WithdrawBusinessCommission;

final class CommissionTest extends TestCase
{
    /** @dataProvider provideDepositOperation() */
    public function testDeposit(Operation $operation): void
    {
        $commission = new DepositCommission();
        $this->assertTrue($commission->supports($operation));
        $this->assertEquals(0.05, $commission->calculate($operation)->getAmount());
    }

    /** @dataProvider provideBusinessWithdrawOperation() */
    public function testBusinessWithdraw(Operation $operation): void
    {
        $commission = new WithdrawBusinessCommission();
        $this->assertTrue($commission->supports($operation));
        $this->assertEquals(1.15, $commission->calculate($operation)->getAmount());
    }

    public function testPrivateWithdrawCommissionOnExceededAmount(): void
    {
        $operation = new Operation(
            new \DateTimeImmutable('now'),
            new User(1, User::TYPE_PRIVATE),
            Operation::TYPE_WITHDRAW,
            Money::ofMinor(150000, Money::EUR)
        );

        $currencyConverter = $this->createMock(CurrencyConverterInterface::class);
        $currencyConverter->method('convert')->willReturn($operation->getAmount());
        $currencyConverter->method('convertBack')->willReturn(Money::ofMinor(100000, Money::EUR));

        $commission = new WithdrawPrivateCommission($currencyConverter);
        $this->assertTrue($commission->supports($operation));
        $this->assertEquals(1.5, $commission->calculate($operation)->getAmount());
    }

    public function testPrivateWithdrawCommissionWeeklyBudget(): void
    {
        $operation1 = new Operation(
            new \DateTimeImmutable('now'),
            new User(1, User::TYPE_PRIVATE),
            Operation::TYPE_WITHDRAW,
            Money::ofMinor(20000, Money::EUR)
        );
        $operation2 = new Operation(
            new \DateTimeImmutable('now'),
            new User(1, User::TYPE_PRIVATE),
            Operation::TYPE_WITHDRAW,
            Money::ofMinor(50000, Money::EUR)
        );
        $operation3 = new Operation(
            new \DateTimeImmutable('now'),
            new User(1, User::TYPE_PRIVATE),
            Operation::TYPE_WITHDRAW,
            Money::ofMinor(30000, Money::EUR)
        );
        $operation4 = new Operation(
            new \DateTimeImmutable('now'),
            new User(1, User::TYPE_PRIVATE),
            Operation::TYPE_WITHDRAW,
            Money::ofMinor(100000, Money::EUR)
        );

        $currencyConverter = $this->createMock(CurrencyConverterInterface::class);
        $currencyConverter
            ->method('convert')
            ->willReturnOnConsecutiveCalls(
                $operation1->getAmount(),
                $operation2->getAmount(),
                $operation3->getAmount(),
                $operation4->getAmount(),
            );
        $currencyConverter
            ->method('convertBack')
            ->willReturnOnConsecutiveCalls(
                $operation1->getAmount(),
                $operation2->getAmount(),
                $operation3->getAmount(),
                $operation4->getAmount(),
            );

        $commission = new WithdrawPrivateCommission($currencyConverter);
        $this->assertTrue($commission->supports($operation1));
        $this->assertEquals(0, $commission->calculate($operation1)->getAmount());
        $this->assertEquals(0, $commission->calculate($operation2)->getAmount());
        $this->assertEquals(0, $commission->calculate($operation3)->getAmount());
        $this->assertEquals(3.0, $commission->calculate($operation4)->getAmount());
    }

    public function provideDepositOperation(): iterable
    {
        yield [
            new Operation(
                new \DateTimeImmutable('now'),
                new User(1, User::TYPE_BUSINESS),
                Operation::TYPE_DEPOSIT,
                Money::ofMinor(15000, Money::EUR)
            ),
        ];
    }

    public function provideBusinessWithdrawOperation(): iterable
    {
        yield [
            new Operation(
                new \DateTimeImmutable('now'),
                new User(1, User::TYPE_BUSINESS),
                Operation::TYPE_WITHDRAW,
                Money::ofMinor(23000, Money::EUR)
            ),
        ];
    }

    public function providePrivateWithdrawOperation(): iterable
    {
        yield [
            [
                new Operation(
                    new \DateTimeImmutable('now'),
                    new User(1, User::TYPE_PRIVATE),
                    Operation::TYPE_WITHDRAW,
                    Money::ofMinor(150000, Money::EUR)
                ),
            ],
            [
                new Operation(
                    new \DateTimeImmutable('now'),
                    new User(2, User::TYPE_BUSINESS),
                    Operation::TYPE_WITHDRAW,
                    Money::ofMinor(3000, Money::EUR)
                ),
            ],
            [
                new Operation(
                    new \DateTimeImmutable('now'),
                    new User(2, User::TYPE_BUSINESS),
                    Operation::TYPE_WITHDRAW,
                    Money::ofMinor(7000, Money::EUR)
                ),
            ],
            [
                new Operation(
                    new \DateTimeImmutable('now'),
                    new User(2, User::TYPE_BUSINESS),
                    Operation::TYPE_WITHDRAW,
                    Money::ofMinor(10000, Money::EUR)
                ),
            ],
        ];
    }
}
