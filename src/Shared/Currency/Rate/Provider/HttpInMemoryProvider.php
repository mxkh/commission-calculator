<?php

declare(strict_types=1);

namespace Acme\Shared\Currency\Rate\Provider;

use Webmozart\Assert\Assert;

final class HttpInMemoryProvider implements ProviderInterface
{
    private string $uri;

    private array $rates = [];


    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    /** @throws \JsonException */
    public function getRateByCode(string $code): float|int
    {
        $all = $this->getAll();

        Assert::keyExists($all['rates'], $code);

        return $all['rates'][$code];
    }

    /** @throws \JsonException */
    public function getAll(): array
    {
        if (!empty($this->rates)) {
            return $this->rates;
        }

        $content = file_get_contents($this->uri);

        return $this->rates = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
