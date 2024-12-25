<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\Model\Day25\Key;
use AdventOfCode\Solution\Model\Day25\Lock;
use AdventOfCode\Solution\SolverInterface;

class Day25 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        [$locks, $keys] = $this->loadSchematics($inputFile);

        $count = 0;
        foreach ($locks as $lock) {
            foreach ($keys as $key) {
                if ($lock->open($key)) {
                    ++$count;
                }
            }
        }

        return (string) $count;
    }

    /**
     * @return array{Lock[], Key[]}
     */
    private function loadSchematics(string $inputFile): array
    {
        $locks = [];
        $keys = [];

        $schematic = null;
        foreach ($this->readInputByLine($inputFile) as $line) {
            if (!$line) {
                if ($schematic instanceof Key) {
                    $keys[] = $schematic;
                } elseif ($schematic instanceof Lock) {
                    $locks[] = $schematic;
                }

                $schematic = null;
            } else {
                if (!$schematic) {
                    if (str_contains($line, '#')) {
                        $schematic = new Lock();
                    } else {
                        $schematic = new Key();
                    }
                } else {
                    $pins = [];

                    foreach (str_split($line) as $char) {
                        $pins[] = $char === '#' ? 1 : 0;
                    }

                    $schematic->addPins($pins);
                }
            }
        }

        if ($schematic instanceof Key) {
            $keys[] = $schematic;
        } elseif ($schematic instanceof Lock) {
            $locks[] = $schematic;
        }

        return [$locks, $keys];
    }
}
