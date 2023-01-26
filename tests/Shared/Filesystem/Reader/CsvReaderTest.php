<?php

declare(strict_types=1);

namespace Acme\Tests\Shared\Filesystem\Reader;

use PHPUnit\Framework\TestCase;
use Acme\Shared\Filesystem\Reader\CsvReader;
use Webmozart\Assert\InvalidArgumentException;
use Acme\Shared\Filesystem\Reader\CsvReaderResult;
use Acme\Shared\Filesystem\Util\StreamReaderInterface;

final class CsvReaderTest extends TestCase
{
    public function testEmptyPathToFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        new CsvReader('');
    }

    public function testWithCustomReader(): void
    {
        $customStreamReader = $this->createMock(StreamReaderInterface::class);
        $customStreamReader
            ->method('readLine')
            ->willReturnOnConsecutiveCalls('2014-12-31,4, private,withdraw,1200.00,EUR', false);

        $reader = new CsvReader('php://memory');
        $reader->setCustomStreamReader($customStreamReader);
        foreach ($reader->read() as $result) {
            $this->assertInstanceOf(CsvReaderResult::class, $result);
            $this->assertEquals('2014-12-31', $result->date);
            $this->assertEquals(4, $result->userId);
            $this->assertEquals('private', $result->userType);
            $this->assertEquals('withdraw', $result->type);
            $this->assertEquals('1200.00', $result->amount);
            $this->assertEquals('EUR', $result->currency);
        }
    }
}
