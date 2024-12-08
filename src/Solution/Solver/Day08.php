<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day08 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $map = $this->loadMap($inputFile);
        $antennas = $this->loadAntennas($map);

        $antiNodesCount = 0;
        $antiNodes = [];

        foreach ($antennas as $antenna => $positions) {
            foreach ($positions as $i => $firstPosition) {
                foreach ($positions as $j => $secondPosition) {
                    if ($i !== $j) {
                        $xDiff = $firstPosition['x'] - $secondPosition['x'];
                        $yDiff = $firstPosition['y'] - $secondPosition['y'];

                        $antiNode = [
                            'x' => $firstPosition['x'] + $xDiff,
                            'y' => $firstPosition['y'] + $yDiff,
                        ];

                        // Check if antinode is inside the map and does not overlap with any antenna
                        if (array_key_exists($antiNode['x'], $map) && array_key_exists($antiNode['y'], $map[$antiNode['x']]) && !isset($antiNodes[$antiNode['x']][$antiNode['y']])) {
                            $antiNodes[$antiNode['x']][$antiNode['y']] = '#' . $antenna;
                            ++$antiNodesCount;
                        }
                    }
                }
            }
        }

        return (string) $antiNodesCount;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $map = $this->loadMap($inputFile);
        $antennas = $this->loadAntennas($map);

        $antiNodesCount = 0;
        $antiNodes = [];

        foreach ($antennas as $antenna => $positions) {
            foreach ($positions as $i => $firstPosition) {
                foreach ($positions as $j => $secondPosition) {
                    if ($i !== $j) {
                        $xDiff = $firstPosition['x'] - $secondPosition['x'];
                        $yDiff = $firstPosition['y'] - $secondPosition['y'];

                        $x = $firstPosition['x'];
                        $y = $firstPosition['y'];

                        while (true) {
                            if (!isset($antiNodes[$x][$y])) {
                                $antiNodes[$x][$y] = '#' . $antenna;
                                ++$antiNodesCount;
                            }

                            $x += $xDiff;
                            $y += $yDiff;

                            if (!array_key_exists($x, $map) || !array_key_exists($y, $map[$x])) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        return (string) $antiNodesCount;
    }

    /**
     * @return array<int, array<int, null|string>>
     */
    private function loadMap(string $inputFile): array
    {
        $map = [];

        foreach ($this->readInputByLine($inputFile) as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                /** @var int $y */
                $map[$x][$y] = $char === '.' ? null : $char;
            }
        }

        return $map;
    }

    /**
     * @param array<int, array<int, null|string>> $map
     *
     * @return array<string, array{x: int, y: int}[]>
     */
    private function loadAntennas(array $map): array
    {
        $antennas = [];

        foreach ($map as $x => $row) {
            foreach ($row as $y => $cell) {
                if ($cell !== null) {
                    $antennas[$cell][] = [
                        'x' => $x,
                        'y' => $y,
                    ];
                }
            }
        }

        return $antennas;
    }
}
