<?php

declare(strict_types=1);

namespace Acme\Commission\Application\Command\CalculateCommissionFee;

use Acme\Commission\Infrastructure\CsvCommissionFeeCalculator;
use Acme\Commission\Infrastructure\CsvRepository;
use Acme\Shared\Filesystem\Reader\CsvReader;

final class CalculateCommissionFeeFromFileHandler
{
    /**
     * @return \Traversable<Output>
     */
    public function handle(CalculateCommissionFeeFromFileCommand $command): \Traversable
    {
        if (!file_exists($command->file)) {
            throw new \LogicException(sprintf('File %s does not exists', $command->file));
        }

        $calculator = new CsvCommissionFeeCalculator(
            new CsvRepository(
                new CsvReader($command->file)
            ),
            $command->exchangeRateUri
        );

        foreach ($calculator->calculate() as $commission) {
            yield new Output($commission);
        }
    }
}
