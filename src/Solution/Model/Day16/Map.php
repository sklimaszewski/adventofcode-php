<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day16;

class Map
{
    /**
     * @var array<int, array<int, bool>>
     */
    private array $tiles = [];

    private ?int $startX = null;

    private ?int $startY = null;

    private ?int $endX = null;

    private ?int $endY = null;

    public function addTile(int $x, int $y, bool $walkable): void
    {
        $this->tiles[$x][$y] = $walkable;
    }

    public function setStart(int $x, int $y): void
    {
        $this->startX = $x;
        $this->startY = $y;
    }

    public function setEnd(int $x, int $y): void
    {
        $this->endX = $x;
        $this->endY = $y;
    }

    public function walk(): Position
    {
        if ($this->startX === null || $this->startY === null || $this->endX === null || $this->endY === null) {
            throw new \Exception('Start or end position not set.');
        }

        $bestScoreRoute = null;
        $visited = [];

        $priorityQueue = new \SplPriorityQueue();
        $priorityQueue->insert(
            new Position(
                $this->startX,
                $this->startY,
                Direction::EAST,
                0,
                0,
            ),
            0 // Initial priority (lowest score is highest priority)
        );

        while (!$priorityQueue->isEmpty()) {
            /** @var Position $position */
            $position = $priorityQueue->extract();

            $x = $position->x;
            $y = $position->y;

            if ($x === $this->endX && $y === $this->endY) {
                $bestScoreRoute = $position;
            }

            // Mark this position as visited with the current score
            if (isset($visited[$x][$y]) && $visited[$x][$y] <= $position->getScore()) {
                continue;
            }
            $visited[$x][$y] = $position->getScore();

            // Explore all possible directions
            $possibleNextTiles = [
                Direction::NORTH->value => ['x' => $x, 'y' => $y - 1],
                Direction::EAST->value => ['x' => $x + 1, 'y' => $y],
                Direction::SOUTH->value => ['x' => $x, 'y' => $y + 1],
                Direction::WEST->value => ['x' => $x - 1, 'y' => $y],
            ];

            /** @var int $direction */
            foreach ($possibleNextTiles as $direction => $next) {
                $nextX = $next['x'];
                $nextY = $next['y'];

                // Skip if the tile is not walkable
                if (empty($this->tiles[$nextX][$nextY])) {
                    continue;
                }

                $nextPosition = new Position(
                    $nextX,
                    $nextY,
                    Direction::from($direction),
                    $position->turns + ($direction !== $position->facing->value ? 1 : 0),
                    $position->steps + 1,
                    $position,
                );

                if (isset($visited[$nextX][$nextY]) && $visited[$nextX][$nextY] <= $nextPosition->getScore()) {
                    continue;
                }

                if ($bestScoreRoute && $nextPosition->getScore() > $bestScoreRoute->getScore()) {
                    continue;
                }

                // Add the new position to the priority queue
                $priorityQueue->insert(
                    $nextPosition,
                    -$nextPosition->steps // Negative priority since SplPriorityQueue orders higher values first
                );
            }
        }

        if ($bestScoreRoute) {
            return $bestScoreRoute;
        }

        // If the queue is empty and no path was found, throw an exception
        throw new \Exception('No possible path found');
    }

    public function multiWalk(): Position
    {
        if ($this->startX === null || $this->startY === null || $this->endX === null || $this->endY === null) {
            throw new \Exception('Start or end position not set.');
        }

        $bestScoreRoute = null;
        $visited = [];

        $priorityQueue = new \SplPriorityQueue();
        $priorityQueue->insert(
            new Position(
                $this->startX,
                $this->startY,
                Direction::EAST,
                0,
                0,
            ),
            0 // Initial priority (lowest score is highest priority)
        );

        while (!$priorityQueue->isEmpty()) {
            /** @var Position $position */
            $position = $priorityQueue->extract();

            $x = $position->x;
            $y = $position->y;

            // Check if we've reached the destination and if the score matches the best score
            if ($x === $this->endX && $y === $this->endY) {
                if ($bestScoreRoute === null || $position->getScore() < $bestScoreRoute->getScore()) {
                    $bestScoreRoute = $position;
                }
            }

            // Initialize visited array for the current position if not set
            if (!isset($visited[$x][$y])) {
                $visited[$x][$y] = [];
            }

            // If this score has already been visited at this position, skip
            foreach ($visited[$x][$y] as $visitedPosition) {
                if ($position->getScore() === $visitedPosition->getScore()) {
                    $position->addAlternativePosition($visitedPosition);
                    $visitedPosition->addAlternativePosition($position);
                    continue 2;
                }
            }

            // Add the current score to the visited list for this position
            $visited[$x][$y][] = $position;

            // Explore all possible directions
            $possibleNextTiles = [
                Direction::NORTH->value => ['x' => $x, 'y' => $y - 1],
                Direction::EAST->value => ['x' => $x + 1, 'y' => $y],
                Direction::SOUTH->value => ['x' => $x, 'y' => $y + 1],
                Direction::WEST->value => ['x' => $x - 1, 'y' => $y],
            ];

            /** @var int $direction */
            foreach ($possibleNextTiles as $direction => $next) {
                $nextX = $next['x'];
                $nextY = $next['y'];

                // Skip if the tile is not walkable
                if (empty($this->tiles[$nextX][$nextY])) {
                    continue;
                }

                if ($position->hasVisited($nextX, $nextY)) {
                    continue;
                }

                $nextPosition = new Position(
                    $nextX,
                    $nextY,
                    Direction::from($direction),
                    $position->turns + ($direction !== $position->facing->value ? 1 : 0),
                    $position->steps + 1,
                    $position,
                );

                if ($bestScoreRoute && $bestScoreRoute->getScore() < $nextPosition->getScore()) {
                    continue;
                }

                echo 'Score: ' . $nextPosition->getScore() . PHP_EOL;

                // Add the new position to the priority queue
                $priorityQueue->insert(
                    $nextPosition,
                    -$nextPosition->steps // Negative priority since SplPriorityQueue orders higher values first
                );
            }
        }

        if ($bestScoreRoute) {
            return $bestScoreRoute;
        }

        // If the queue is empty and no path was found, throw an exception
        throw new \Exception('No possible path found');
    }
}
