<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day22;

readonly class Price
{
    public function __construct(
        public int $change,
        public int $value,
    ) {
    }
}
