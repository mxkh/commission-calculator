<?php

declare(strict_types=1);

namespace Acme\Tests\Commission\Domain;

use PHPUnit\Framework\TestCase;
use Acme\Commission\Domain\User;
use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\Operation;
use Webmozart\Assert\InvalidArgumentException;

final class OperationTest extends TestCase
{
    public function testInvariants(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected one of: "deposit", "withdraw". Got: "type"');

        new Operation(
            new \DateTimeImmutable('now'),
            new User(1, User::TYPE_BUSINESS),
            'type',
            Money::ofMinor(100, Money::EUR)
        );
    }
}
