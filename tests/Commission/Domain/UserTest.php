<?php

declare(strict_types=1);

namespace Acme\Tests\Commission\Domain;

use PHPUnit\Framework\TestCase;
use Acme\Commission\Domain\User;
use Webmozart\Assert\InvalidArgumentException;

final class UserTest extends TestCase
{
    public function testUserSupportedTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected one of: "private", "business". Got: "type"');
        new User(1, 'type');
    }

    public function testUserEmptyType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        new User(1, '');
    }
}
