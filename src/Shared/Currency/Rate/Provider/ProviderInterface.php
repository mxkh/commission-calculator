<?php

declare(strict_types=1);

namespace Acme\Shared\Currency\Rate\Provider;

interface ProviderInterface
{
    public function getAll(): array;

    public function getRateByCode(string $code): float|int;
}
