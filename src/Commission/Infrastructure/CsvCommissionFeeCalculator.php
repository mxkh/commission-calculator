<?php

declare(strict_types=1);

namespace Acme\Commission\Infrastructure;

use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\IterableRepositoryInterface;
use Acme\Commission\Domain\CommissionFeeCalculatorContext;
use Acme\Commission\Domain\CommissionFeeCalculatorInterface;
use Acme\Shared\Currency\Rate\Provider\HttpInMemoryProvider;

final class CsvCommissionFeeCalculator implements CommissionFeeCalculatorInterface
{
    private CsvRepository $repository;

    private CommissionFeeCalculatorContext $context;

    public function __construct(IterableRepositoryInterface $repository, string $exchangeRateUri)
    {
        $this->repository = $repository;
        $this->context = new CommissionFeeCalculatorContext(
            [
                new DepositCommission(),
                new WithdrawBusinessCommission(),
                new WithdrawPrivateCommission(new CurrencyConverter(new HttpInMemoryProvider($exchangeRateUri))),
            ]
        );
    }

    /** @return \Traversable<Money> */
    public function calculate(): \Traversable
    {
        foreach ($this->repository->iterate() as $operation) {
            $commission = $this->context->execute($operation);
            yield $commission->calculate($operation);
        }
    }
}
