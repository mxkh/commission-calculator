<?php

declare(strict_types=1);

namespace Acme\Commission\Domain;

use Webmozart\Assert\Assert;

final class CommissionFeeCalculatorContext
{
    private array $commissions;

    /**
     * @param array<CommissionInterface> $commissions
     */
    public function __construct(array $commissions)
    {
        Assert::allIsInstanceOf($commissions, CommissionInterface::class);
        $this->commissions = $commissions;
    }

    public function execute(Operation $operation): CommissionInterface
    {
        foreach ($this->commissions as $commission) {
            if ($commission->supports($operation)) {
                return $commission;
            }
        }

        throw new \LogicException('Commission strategy was not found');
    }
}
