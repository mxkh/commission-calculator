<?php

declare(strict_types=1);

namespace Acme\Commission\Domain;

use Webmozart\Assert\Assert;

final class Operation
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';

    private \DateTimeImmutable $date;

    private User $user;

    private string $type;

    private Money $amount;

    public function __construct(\DateTimeImmutable $date, User $user, string $type, Money $amount)
    {
        Assert::inArray($type, $this->getAvailableTypes());

        $this->date = $date;
        $this->user = $user;
        $this->type = $type;
        $this->amount = $amount;
    }

    public function getAvailableTypes(): array
    {
        return [self::TYPE_DEPOSIT, self::TYPE_WITHDRAW];
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
