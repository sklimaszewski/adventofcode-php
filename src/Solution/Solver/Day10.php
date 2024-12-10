<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day10 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $map = $this->loadMap($inputFile);
        $trailheads = $this->getTrailheads($map);

        $score = 0;
        foreach ($trailheads as $trailhead) {
            [$x, $y] = $trailhead;
            $tops = $this->walk($map, [$x, $y, 0]);

            $score += count($tops);
        }

        return (string) $score;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $map = $this->loadMap($inputFile);
        $trailheads = $this->getTrailheads($map);

        $score = 0;
        foreach ($trailheads as $trailhead) {
            [$x, $y] = $trailhead;

            $routes = [];
            $this->walk($map, [$x, $y, 0], [], $routes);

            foreach ($routes as $paths) {
                $score += count($paths);
            }
        }

        return (string) $score;
    }

    /**
     * @param array<int, array<int, int>>   $map
     * @param array{0: int, 1: int, 2: int} $position
     * @param array{0: int, 1: int}[]       $path
     * @param array<string, string[]>       $routes
     *
     * @return array{0: int, 1: int}[]
     */
    private function walk(array $map, array $position, array $path = [], array &$routes = []): array
    {
        [$x, $y, $level] = $position;
        $path[] = [$x, $y];

        if ($level === 9) {
            $route = json_encode($path);
            if (is_string($route) && !in_array($route, $routes[$x . '-' . $y] ?? [], true)) {
                $routes[$x . '-' . $y][] = $route;
            }

            return [
                [$x, $y],
            ];
        }

        $potentialMoves = [
            [$x - 1, $y],
            [$x + 1, $y],
            [$x, $y - 1],
            [$x, $y + 1],
        ];

        $tops = [];

        foreach ($potentialMoves as $move) {
            [$moveX, $moveY] = $move;
            if (!in_array($move, $path, true) && isset($map[$moveX][$moveY]) && $map[$moveX][$moveY] === ($level + 1)) {
                foreach ($this->walk($map, [$moveX, $moveY, $level + 1], $path, $routes) as $top) {
                    if (!in_array($top, $tops, true)) {
                        $tops[] = $top;
                    }
                }
            }
        }

        return $tops;
    }

    /**
     * @return array<int, array<int, int>>
     */
    private function loadMap(string $inputFile): array
    {
        $map = [];

        foreach ($this->readInputByLine($inputFile) as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                /** @var int $y */
                $map[$x][$y] = intval($char);
            }
        }

        return $map;
    }

    /**
     * @param array<int, array<int, int>> $map
     *
     * @return array<int, array{0: int, 1: int}>
     */
    private function getTrailheads(array $map): array
    {
        $trailheads = [];

        foreach ($map as $x => $row) {
            foreach ($row as $y => $value) {
                if ($value === 0) {
                    $trailheads[] = [$x, $y];
                }
            }
        }

        return $trailheads;
    }
}
