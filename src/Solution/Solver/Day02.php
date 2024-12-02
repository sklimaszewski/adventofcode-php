<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day02 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $reports = 0;

        foreach ($this->readInputByLine($inputFile) as $line) {
            $values = $this->parseIntValues($line);

            if ($this->isReportValid($values)) {
                ++$reports;
            }
        }

        return (string) $reports;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $reports = 0;

        foreach ($this->readInputByLine($inputFile) as $line) {
            $values = $this->parseIntValues($line);

            if ($this->isReportValid($values)) {
                ++$reports;
            } else {
                foreach ($values as $i => $level) {
                    $copy = [...$values];
                    unset($copy[$i]);

                    if ($this->isReportValid(array_values($copy))) {
                        ++$reports;
                        break;
                    }
                }
            }
        }

        return (string) $reports;
    }

    /**
     * @param int[] $levels
     */
    public function isReportValid(array $levels): bool
    {
        $diffDir = 0;

        for ($i = 1; $i < count($levels); ++$i) {
            $diff = $levels[$i] - $levels[$i - 1];
            $absDiff = abs($diff);

            if ($absDiff < 1 || $absDiff > 3) {
                return false;
            }

            if ($diffDir === 0) {
                $diffDir = $diff;
            } elseif (($diffDir < 0 && $diff > 0) || ($diffDir > 0 && $diff < 0)) {
                return false;
            }
        }

        return true;
    }
}
