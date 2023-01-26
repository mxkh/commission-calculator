<?php

declare(strict_types=1);

namespace Acme\Commission\Domain;

interface CommissionFeeCalculatorInterface
{
    public function calculate(): \Traversable;
}
