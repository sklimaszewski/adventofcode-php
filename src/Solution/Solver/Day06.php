<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day06 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $loaded = $this->loadMap($inputFile);

        $tiles = $loaded['tiles'];
        $facing = $loaded['facing'];
        $posX = $loaded['posX'];
        $posY = $loaded['posY'];

        // Walk the paths
        $this->walk($tiles, $posX, $posY, $facing);

        $visited = 0;
        foreach ($tiles as $row) {
            foreach ($row as $tile) {
                if ($tile['visited']) {
                    ++$visited;
                }
            }
        }

        return (string) $visited;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $loaded = $this->loadMap($inputFile);

        $startingTiles = $loaded['tiles'];
        $startingPosX = $loaded['posX'];
        $startingPosY = $loaded['posY'];

        // Try new blocking positions
        $result = 0;
        foreach ($startingTiles as $blockingX => $row) {
            foreach ($row as $blockingY => $tile) {
                if ($tile['blocking'] || ($blockingX === $startingPosX && $blockingY === $startingPosY)) {
                    // Already blocking or starting position - skip
                    continue;
                }

                // "Reset" the map
                $tiles = $loaded['tiles'];
                $tiles[$blockingX][$blockingY]['blocking'] = true;

                $posX = $loaded['posX'];
                $posY = $loaded['posY'];

                try {
                    $this->walk($tiles, $posX, $posY, $loaded['facing']);
                } catch (\Exception) {
                    // Stucked in a loop - we found the solution
                    ++$result;
                }
            }
        }

        return (string) $result;
    }

    /**
     * @return array{tiles: array<int, array<int, array{blocking: bool, visited: bool}>>, facing: string, posX: int, posY: int}
     */
    private function loadMap(string $inputFile): array
    {
        /** @var array<int, array<int, array{blocking: bool, visited: bool}>> $tiles */
        $tiles = [];

        $facing = '^';
        $posX = 0;
        $posY = 0;

        foreach ($this->readInputByLine($inputFile) as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                /** @var int $y */
                $tiles[$x][$y] = [
                    'blocking' => $char === '#',
                    'visited' => false,
                ];

                if (in_array($char, ['^', 'v', '<', '>'], true)) {
                    $tiles[$x][$y]['visited'] = true;
                    $posX = intval($x);
                    $posY = $y;
                    $facing = $char;
                }
            }
        }

        return [
            'tiles' => $tiles,
            'facing' => $facing,
            'posX' => $posX,
            'posY' => $posY,
        ];
    }

    /**
     * Throws an exception when stucked in a loop.
     *
     * @param array<int, array<int, array{blocking: bool, visited: bool}>> $tiles
     *
     * @throws \Exception
     */
    private function walk(array &$tiles, int &$posX, int &$posY, string $facing): void
    {
        $maxVisitedPositions = count($tiles) + 1;

        // Walk the paths
        while (true) {
            switch ($facing) {
                case '^':
                    $posY--;
                    break;
                case 'v':
                    $posY++;
                    break;
                case '<':
                    $posX--;
                    break;
                case '>':
                    $posX++;
                    break;
            }

            // Check if we hit an edge
            if (!isset($tiles[$posX][$posY])) {
                break;
            }

            // Check if we hit a blocking tile
            if ($tiles[$posX][$posY]['blocking']) {
                // Back to the previous position + rotate 90 degrees to the right
                switch ($facing) {
                    case '^':
                        $posY++;
                        $facing = '>';
                        break;
                    case 'v':
                        $posY--;
                        $facing = '<';
                        break;
                    case '<':
                        $posX++;
                        $facing = '^';
                        break;
                    case '>':
                        $posX--;
                        $facing = 'v';
                        break;
                }
            } elseif ($tiles[$posX][$posY]['visited']) {
                --$maxVisitedPositions;
                if ($maxVisitedPositions === 0) {
                    throw new \Exception('Stucked in a loop');
                }
            } else {
                $maxVisitedPositions = count($tiles) + 1;
                $tiles[$posX][$posY]['visited'] = true;
            }
        }
    }
}
