<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day20;

readonly class Position
{
    public function __construct(
        public int $x,
        public int $y,
        public int $steps,
        public int $cheatSteps = 0,
        public ?self $previousPosition = null,
    ) {
    }

    public function hasVisited(int $x, int $y): bool
    {
        if ($this->x === $x && $this->y === $y) {
            return true;
        }

        if ($this->previousPosition === null) {
            return false;
        }

        return $this->previousPosition->hasVisited($x, $y);
    }

    /**
     * @param array<int, array{int, int}> $track
     *
     * @return array<int, array{int, int}>
     */
    public function getPositions(array &$track = []): array
    {
        if ($this->previousPosition) {
            $this->previousPosition->getPositions($track);
        }

        $track[] = [$this->x, $this->y];

        return $track;
    }
}
