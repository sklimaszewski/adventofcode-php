<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day12 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $map = $this->loadMap($inputFile);

        $areas = [];
        foreach ($map as $x => $row) {
            foreach ($row as $y => $value) {
                $area = [
                    'x' => $x,
                    'y' => $y,
                    'value' => $value,
                    'perimeter' => 0,
                ];

                $sides = [
                    [$x - 1, $y],
                    [$x + 1, $y],
                    [$x, $y - 1],
                    [$x, $y + 1],
                ];
                foreach ($sides as $side) {
                    [$sideX, $sideY] = $side;
                    if (!isset($map[$sideX][$sideY]) || $map[$sideX][$sideY] !== $value) {
                        ++$area['perimeter'];
                    }
                }

                $areas[] = $area;
            }
        }

        // Merge areas with the same value and touching each other
        $regions = [];

        while (!empty($areas)) {
            $area = array_pop($areas);
            $currentRegion = [
                'value' => $area['value'],
                'perimeter' => $area['perimeter'],
                'areas' => [
                    [$area['x'], $area['y']],
                ],
            ];

            /** @var array<int, array<int, int>> $queue */
            $queue = [
                [$area['x'], $area['y']],
            ];

            while (!empty($queue)) {
                [$x, $y] = array_pop($queue);
                foreach ($areas as $i => $area) {
                    if ($area['value'] === $currentRegion['value']) {
                        $sides = [
                            [$x - 1, $y],
                            [$x + 1, $y],
                            [$x, $y - 1],
                            [$x, $y + 1],
                        ];

                        foreach ($sides as $side) {
                            if ($side[0] === $area['x'] && $side[1] === $area['y']) {
                                $currentRegion['areas'][] = [$area['x'], $area['y']];
                                $currentRegion['perimeter'] += $area['perimeter'];
                                $queue[] = [$area['x'], $area['y']];
                                unset($areas[$i]);
                            }
                        }
                    }
                }
            }

            $regions[] = $currentRegion;
        }

        $total = 0;

        foreach ($regions as $region) {
            $total += count($region['areas']) * $region['perimeter'];
        }

        return (string) $total;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $map = $this->loadMap($inputFile);

        $areas = [];
        foreach ($map as $x => $row) {
            foreach ($row as $y => $value) {
                $areas[] = [
                    'x' => $x,
                    'y' => $y,
                    'value' => $value,
                ];
            }
        }

        // Merge areas with the same value and touching each other
        $total = 0;

        while (!empty($areas)) {
            $area = array_pop($areas);

            $regionValue = $area['value'];
            $regionAreas = [
                [$area['x'], $area['y']],
            ];

            /** @var array<int, array<int, int>> $queue */
            $queue = [
                [$area['x'], $area['y']],
            ];

            while (!empty($queue)) {
                [$x, $y] = array_pop($queue);
                foreach ($areas as $i => $area) {
                    if ($area['value'] === $regionValue) {
                        $directions = [
                            [$x - 1, $y],
                            [$x + 1, $y],
                            [$x, $y - 1],
                            [$x, $y + 1],
                        ];

                        foreach ($directions as $direction) {
                            [$directionX, $directionY] = $direction;
                            if ($directionX === $area['x'] && $directionY === $area['y']) {
                                $regionAreas[] = [$area['x'], $area['y']];
                                $queue[] = [$area['x'], $area['y']];
                                unset($areas[$i]);
                            }
                        }
                    }
                }
            }

            $sides = $this->countHorizontalSides($regionAreas) + $this->countVerticalSides($regionAreas);

            $total += count($regionAreas) * $sides;
        }

        return (string) $total;
    }

    /**
     * @param array<int, array<int, int>> $points
     */
    private function countHorizontalSides(array $points): int
    {
        $minY = 0;
        $maxY = 0;
        foreach ($points as $point) {
            [$x, $y] = $point;
            if ($y < $minY) {
                $minY = $y;
            }
            if ($y > $maxY) {
                $maxY = $y;
            }
        }

        $sides = 0;
        for ($y = ($minY - 1); $y <= $maxY; ++$y) {
            $yPoints = [];
            foreach ($points as $point) {
                [$pointX, $pointY] = $point;
                if ($pointY === $y) {
                    $yPoints[] = $pointX;
                }
            }

            $nextYPoints = [];
            foreach ($points as $point) {
                [$pointX, $pointY] = $point;
                if ($pointY === $y + 1) {
                    $nextYPoints[] = $pointX;
                }
            }

            sort($yPoints);
            sort($nextYPoints);

            $diff = array_values(array_diff($yPoints, $nextYPoints));
            if ($diff) {
                $sides += 1 + $this->countNumberGaps($diff);
            }

            $diff = array_values(array_diff($nextYPoints, $yPoints));
            if ($diff) {
                $sides += 1 + $this->countNumberGaps($diff);
            }
        }

        return $sides;
    }

    /**
     * @param array<int, array<int, int>> $points
     */
    private function countVerticalSides(array $points): int
    {
        $minX = 0;
        $maxX = 0;
        foreach ($points as $point) {
            [$x, $y] = $point;
            if ($x < $minX) {
                $minX = $x;
            }
            if ($x > $maxX) {
                $maxX = $x;
            }
        }

        $sides = 0;
        for ($x = ($minX - 1); $x <= $maxX; ++$x) {
            $xPoints = [];
            foreach ($points as $point) {
                [$pointX, $pointY] = $point;
                if ($pointX === $x) {
                    $xPoints[] = $pointY;
                }
            }

            $nextXPoints = [];
            foreach ($points as $point) {
                [$pointX, $pointY] = $point;
                if ($pointX === $x + 1) {
                    $nextXPoints[] = $pointY;
                }
            }

            sort($xPoints);
            sort($nextXPoints);

            $diff = array_values(array_diff($xPoints, $nextXPoints));
            if ($diff) {
                $sides += 1 + $this->countNumberGaps($diff);
            }

            $diff = array_values(array_diff($nextXPoints, $xPoints));
            if ($diff) {
                $sides += 1 + $this->countNumberGaps($diff);
            }
        }

        return $sides;
    }

    /**
     * @param int[] $numbers
     */
    private function countNumberGaps(array $numbers): int
    {
        $gaps = 0;

        // Sort the array to ensure numbers are in order
        sort($numbers);

        // Iterate through the array and check gaps
        for ($i = 0; $i < count($numbers) - 1; ++$i) {
            if ($numbers[$i + 1] - $numbers[$i] > 1) {
                ++$gaps;
            }
        }

        return $gaps;
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function loadMap(string $inputFile): array
    {
        $map = [];

        foreach ($this->readInputByLine($inputFile) as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                /** @var int $y */
                $map[$x][$y] = $char;
            }
        }

        return $map;
    }
}
