<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day13;

readonly class Game
{
    public function __construct(
        public Button $aButton,
        public Button $bButton,
        public int $prizeX,
        public int $prizeY,
    ) {
    }
}
