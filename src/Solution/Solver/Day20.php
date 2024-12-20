<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\Model\Day20\Map;
use AdventOfCode\Solution\SolverInterface;

class Day20 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $map = $this->loadMap($inputFile);

        return (string) $map->countCheatRoutes(100);
    }

    public function solveSecondPart(string $inputFile): string
    {
        $map = $this->loadMap($inputFile);

        return (string) $map->countCheatRoutes(100, 20);
    }

    private function loadMap(string $inputFile): Map
    {
        $map = new Map();

        /** @var int $y */
        foreach ($this->readInputByLine($inputFile) as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                if ($char === 'S') {
                    $map->setStart($x, $y);
                    $map->addTile($x, $y, true);
                } elseif ($char === 'E') {
                    $map->setEnd($x, $y);
                    $map->addTile($x, $y, true);
                } elseif ($char === '#') {
                    $map->addTile($x, $y, false);
                } elseif ($char === '.') {
                    $map->addTile($x, $y, true);
                }
            }
        }

        return $map;
    }
}
