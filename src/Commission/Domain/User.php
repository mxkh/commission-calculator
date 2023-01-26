<?php

declare(strict_types=1);

namespace Acme\Commission\Domain;

use Webmozart\Assert\Assert;

final class User
{
    public const TYPE_PRIVATE = 'private';
    public const TYPE_BUSINESS = 'business';

    private int $id;

    private string $type;

    public function __construct(int $id, string $type)
    {
        Assert::notEmpty($type);
        Assert::inArray($type, $this->getAvailableTypes());

        $this->id = $id;
        $this->type = $type;
    }

    public function getAvailableTypes(): array
    {
        return [self::TYPE_PRIVATE, self::TYPE_BUSINESS];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
