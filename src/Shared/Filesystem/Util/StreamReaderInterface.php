<?php

declare(strict_types=1);

namespace Acme\Shared\Filesystem\Util;

interface StreamReaderInterface
{
    public function readLine($resource): false|string;
}
