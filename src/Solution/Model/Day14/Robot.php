<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day14;

class Robot
{
    public function __construct(
        public int $x,
        public int $y,
        public readonly int $xMove,
        public readonly int $yMove,
    ) {
    }
}
