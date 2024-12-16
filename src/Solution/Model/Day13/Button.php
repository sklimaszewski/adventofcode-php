<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day13;

readonly class Button
{
    public function __construct(
        public int $xMove,
        public int $yMove,
        public int $tokenCost,
    ) {
    }
}
