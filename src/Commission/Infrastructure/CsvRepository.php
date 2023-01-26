<?php

declare(strict_types=1);

namespace Acme\Commission\Infrastructure;

use Acme\Commission\Domain\IterableRepositoryInterface;
use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\Operation;
use Acme\Commission\Domain\User;
use Acme\Shared\Filesystem\Reader\FileReaderInterface;

final class CsvRepository implements IterableRepositoryInterface
{
    private FileReaderInterface $fileReader;

    public function __construct(FileReaderInterface $fileReader)
    {
        $this->fileReader = $fileReader;
    }

    public function iterate(): \Iterator
    {
        $result = $this->fileReader->read();

        foreach ($result as $csvResult) {
            yield new Operation(
                \DateTimeImmutable::createFromFormat('Y-m-d', $csvResult->date),
                new User($csvResult->userId, $csvResult->userType),
                $csvResult->type,
                Money::parseCurrencyFromString($csvResult->amount, $csvResult->currency)->toMinor()
            );
        }
    }
}
