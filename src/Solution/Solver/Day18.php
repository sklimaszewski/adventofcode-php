<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day18 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $obstacles = $this->loadObstacles($inputFile);

        return (string) $this->findShortestPath($obstacles, 1024);
    }

    public function solveSecondPart(string $inputFile): string
    {
        $obstacles = $this->loadObstacles($inputFile);

        $minBytes = 1024;
        $maxBytes = count($obstacles) - 1;

        $bytes = (int) ceil(($minBytes + $maxBytes) / 2);

        while ($minBytes < $maxBytes) {
            if ($this->canFindPath($obstacles, $bytes)) {
                $minBytes = $bytes + 1;
            } else {
                $maxBytes = $bytes;
            }

            $bytes = (int) ceil(($minBytes + $maxBytes) / 2);
        }

        return implode(',', $obstacles[$bytes - 1]);
    }

    /**
     * @param array<int, array{int, int}> $obstacles
     */
    private function canFindPath(array $obstacles, int $bytes): bool
    {
        try {
            $this->findShortestPath($obstacles, $bytes);

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * @param array<int, array{int, int}> $obstacles
     *
     * @throws \Exception
     */
    private function findShortestPath(array $obstacles, int $bytes): int
    {
        $obstacles = array_slice($obstacles, 0, $bytes);

        $maxX = 70;
        $maxY = 70;

        $visited = [];

        $priorityQueue = new \SplPriorityQueue();
        $priorityQueue->insert(
            [
                'x' => 0,
                'y' => 0,
                'visitedTiles' => [
                    [0, 0],
                ],
            ],
            0,
        );

        while (!$priorityQueue->isEmpty()) {
            /** @var array{x: int, y: int, visitedTiles: array{int, int}[]} $position */
            $position = $priorityQueue->extract();

            $x = $position['x'];
            $y = $position['y'];
            $visitedTiles = $position['visitedTiles'];

            if ($x === $maxX && $y === $maxY) {
                return count($visitedTiles) - 1;
            }

            if ($visited[$x][$y] ?? false) {
                continue;
            }

            $visited[$x][$y] = true;

            $possibleNextTiles = [
                ['x' => $x, 'y' => $y - 1],
                ['x' => $x + 1, 'y' => $y],
                ['x' => $x, 'y' => $y + 1],
                ['x' => $x - 1, 'y' => $y],
            ];

            foreach ($possibleNextTiles as $tile) {
                $nextX = $tile['x'];
                $nextY = $tile['y'];

                if ($nextX < 0 || $nextY < 0 || $nextX > $maxX || $nextY > $maxY) {
                    continue;
                }

                if (in_array([$nextX, $nextY], $visitedTiles, true)) {
                    continue;
                }

                if (in_array([$nextX, $nextY], $obstacles, true)) {
                    continue;
                }

                $priorityQueue->insert(
                    [
                        'x' => $nextX,
                        'y' => $nextY,
                        'visitedTiles' => [...$visitedTiles, [$nextX, $nextY]],
                    ],
                    -count($visitedTiles),
                );
            }
        }

        throw new \Exception('Cannot find path');
    }

    /**
     * @return array<int, array{int, int}>
     */
    private function loadObstacles(string $inputFile): array
    {
        $obstacles = [];

        /** @var int $i */
        foreach ($this->readInputByLine($inputFile) as $i => $line) {
            [$x, $y] = array_map('intval', explode(',', $line));
            $obstacles[$i] = [$x, $y];
        }

        return $obstacles;
    }
}
