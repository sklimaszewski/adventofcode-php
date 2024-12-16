<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day16;

class Position
{
    /**
     * @var Position[]
     */
    private array $alternativePositions = [];

    public function __construct(
        public int $x,
        public int $y,
        public Direction $facing,
        public int $turns,
        public int $steps,
        public ?self $previousPosition = null,
    ) {
    }

    public function addAlternativePosition(self $position): void
    {
        $this->alternativePositions[] = $position;
    }

    public function getScore(): int
    {
        return $this->steps + ($this->turns * 1000);
    }

    public function hasVisited(int $x, int $y): bool
    {
        $tiles = $this->getVisitedTiles();

        return in_array([$x, $y], $tiles, true);
    }

    /**
     * @param Position[] $visitedPositions
     *
     * @return array<int, array{int, int}>
     */
    public function getVisitedTiles(&$visitedPositions = []): array
    {
        $visitedPositions[] = $this;

        if ($this->previousPosition && !in_array($this->previousPosition, $visitedPositions, true)) {
            $tiles = $this->previousPosition->getVisitedTiles($visitedPositions);
        } else {
            $tiles = [];
        }

        foreach ($this->alternativePositions as $alternativePosition) {
            if (!in_array($alternativePosition, $visitedPositions, true)) {
                $tiles = array_merge($tiles, $alternativePosition->getVisitedTiles($visitedPositions));
            }
        }

        $tiles[] = [$this->x, $this->y];

        return array_unique($tiles, SORT_REGULAR);
    }
}
