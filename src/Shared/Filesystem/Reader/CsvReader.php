<?php

declare(strict_types=1);

namespace Acme\Shared\Filesystem\Reader;

use Acme\Shared\Filesystem\Util\StreamReaderInterface;
use Webmozart\Assert\Assert;

final class CsvReader implements FileReaderInterface
{
    private string $path;

    private ?StreamReaderInterface $customStreamReader = null;

    public function __construct(string $path)
    {
        Assert::notEmpty($path);
        $this->path = $path;
    }

    public function read(): \Iterator
    {
        $file = fopen($this->path, 'rb');

        try {
            while ($line = $this->readLine($file)) {
                $values = array_map('trim', explode(',', $line));
                yield new CsvReaderResult(
                    $values['0'],
                    (int) $values['1'],
                    $values['2'],
                    $values['3'],
                    $values['4'],
                    $values['5'],
                );
            }
        } finally {
            fclose($file);
        }
    }

    private function readLine(mixed $resource): false|string
    {
        if (null === $this->customStreamReader) {
            return fgets($resource);
        }

        return $this->customStreamReader->readLine($resource);
    }

    public function setCustomStreamReader(StreamReaderInterface $streamReader): self
    {
        $this->customStreamReader = $streamReader;

        return $this;
    }
}
