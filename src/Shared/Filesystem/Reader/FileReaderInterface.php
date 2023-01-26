<?php

declare(strict_types=1);

namespace Acme\Shared\Filesystem\Reader;

interface FileReaderInterface
{
    public function read(): \Iterator;
}
