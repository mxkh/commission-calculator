<?php

declare(strict_types=1);

namespace Acme\Commission\Domain;

interface CommissionInterface
{
    public function calculate(Operation $operation): Money;

    public function supports(Operation $operation): bool;
}
