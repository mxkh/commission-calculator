<?php

declare(strict_types=1);

namespace Acme\Commission\Domain;

interface IterableRepositoryInterface
{
    public function iterate(): \Iterator;
}
