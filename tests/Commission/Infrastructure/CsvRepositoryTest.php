<?php

declare(strict_types=1);

namespace Acme\Tests\Commission\Infrastructure;

use PHPUnit\Framework\TestCase;
use Acme\Commission\Domain\User;
use Acme\Commission\Domain\Money;
use Acme\Commission\Domain\Operation;
use Acme\Commission\Infrastructure\CsvRepository;
use Acme\Shared\Filesystem\Reader\CsvReaderResult;
use Acme\Shared\Filesystem\Reader\FileReaderInterface;

final class CsvRepositoryTest extends TestCase
{
    /** @dataProvider provideCsvResult() */
    public function testRepository(CsvReaderResult $csvReaderResult): void
    {
        $reader = $this->createMock(FileReaderInterface::class);
        $reader->expects($this->once())->method('read')->willReturn(new \ArrayIterator([$csvReaderResult]));

        $repository = new CsvRepository($reader);
        foreach ($repository->iterate() as $object) {
            $this->assertInstanceOf(Operation::class, $object);
            $this->assertEquals(1, $object->getUser()->getId());
            $this->assertEquals(User::TYPE_BUSINESS, $object->getUser()->getType());
            $this->assertEquals(Operation::TYPE_DEPOSIT, $object->getType());
            $this->assertEquals(100, $object->getAmount()->getAmount());
            $this->assertEquals(Money::EUR, $object->getAmount()->getCurrency());
        }
    }

    public function provideCsvResult(): iterable
    {
        yield [
            new CsvReaderResult(
                (new \DateTimeImmutable('now'))->format('Y-m-d'),
                1,
                User::TYPE_BUSINESS,
                Operation::TYPE_DEPOSIT,
                '1',
                Money::EUR
            ),
        ];
    }
}
