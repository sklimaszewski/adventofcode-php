<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day01 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        // Split input data into two groups
        [$firstGroup, $secondGroup] = $this->prepareInputData($inputFile);

        sort($firstGroup);
        sort($secondGroup);

        // Find the total distance between the two groups locations
        $total = 0;
        foreach ($firstGroup as $i => $location) {
            $total += abs($location - $secondGroup[$i]);
        }

        return (string) $total;
    }

    public function solveSecondPart(string $inputFile): string
    {
        // Split input data into two groups
        [$firstGroup, $secondGroup] = $this->prepareInputData($inputFile);

        $secondGroupValues = array_count_values($secondGroup);

        $total = 0;
        foreach ($firstGroup as $number) {
            $total += $number * ($secondGroupValues[$number] ?? 0);
        }

        return (string) $total;
    }

    /**
     * Splits input data into two groups of integers.
     */
    private function prepareInputData(string $inputFile): array
    {
        $firstGroup = [];
        $secondGroup = [];

        foreach ($this->readInputByLine($inputFile) as $line) {
            $locations = explode(' ', preg_replace('/\s+/', ' ', $line));

            $firstGroup[] = intval($locations[0] ?? 0);
            $secondGroup[] = intval($locations[1] ?? 0);
        }

        return [$firstGroup, $secondGroup];
    }
}
