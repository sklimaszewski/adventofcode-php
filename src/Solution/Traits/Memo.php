<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Traits;

trait Memo
{
    /**
     * @var array<string, mixed>
     */
    private static array $memo;

    /**
     * @template T
     *
     * @phpstan-param callable(): T $callback
     *
     * @param string[] $keys
     *
     * @return T
     */
    protected function memo(callable $callback, array $keys)
    {
        $key = implode('-', $keys);

        if (!isset(self::$memo[$key])) {
            self::$memo[$key] = $callback();
        }

        /** @var T */
        return self::$memo[$key];
    }
}
