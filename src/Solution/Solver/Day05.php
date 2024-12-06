<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day05 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $map = [];
        $result = 0;

        foreach ($this->readInputByLine($inputFile) as $line) {
            if (str_contains($line, '|')) {
                $map[] = $this->parseIntValues($line, '|');
            } if (str_contains($line, ',')) {
                $values = $this->parseIntValues($line, ',');

                $valid = true;
                foreach ($values as $i => $first) {
                    foreach (array_slice($values, $i + 1) as $second) {
                        if ($this->compare($first, $second, $map) > 0) {
                            $valid = false;
                            break;
                        }
                    }

                    if (!$valid) {
                        break;
                    }
                }

                if ($valid) {
                    $result += $values[ceil(count($values) / 2) - 1];
                }
            }
        }

        return (string) $result;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $map = [];
        $result = 0;

        foreach ($this->readInputByLine($inputFile) as $line) {
            if (str_contains($line, '|')) {
                $map[] = $this->parseIntValues($line, '|');
            } if (str_contains($line, ',')) {
                $values = $this->parseIntValues($line, ',');

                $valid = true;
                foreach ($values as $i => $first) {
                    foreach (array_slice($values, $i + 1) as $second) {
                        if ($this->compare($first, $second, $map) > 0) {
                            $valid = false;
                            break;
                        }
                    }

                    if (!$valid) {
                        break;
                    }
                }

                if (!$valid) {
                    usort($values, fn ($a, $b) => $this->compare($a, $b, $map));

                    $result += $values[(int) floor(count($values) / 2)];
                }
            }
        }

        return (string) $result;
    }

    /**
     * @param int[][] $map
     */
    private function compare(int $first, int $second, array $map): int
    {
        foreach ($map as $values) {
            if (in_array($first, $values, true) && in_array($second, $values, true)) {
                $firstIndex = array_search($first, $values, true);
                $secondIndex = array_search($second, $values, true);

                return intval($firstIndex) - intval($secondIndex);
            }
        }

        return 0;
    }
}
