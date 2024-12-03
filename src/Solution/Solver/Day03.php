<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day03 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $input = $this->readInput($inputFile);
        preg_match_all('/mul\((\d+),(\d+)\)/', $input, $matches);

        $result = 0;
        foreach ($matches[1] as $key => $firstNumber) {
            $result += intval($firstNumber) * intval($matches[2][$key]);
        }

        return (string) $result;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $input = str_replace("\n", '', $this->readInput($inputFile));
        preg_match_all('/mul\((\d+),(\d+)\)/', $input, $matches, PREG_OFFSET_CAPTURE);

        $lastMultiplicationPosition = 0;
        $multiplicationEnabled = true;

        $result = 0;
        foreach ($matches[0] as $key => $match) {
            $inputPart = substr($input, $lastMultiplicationPosition, $match[1] - $lastMultiplicationPosition);
            if (str_contains($inputPart, 'do()')) {
                $multiplicationEnabled = true;
            } elseif (str_contains($inputPart, 'don\'t()')) {
                $multiplicationEnabled = false;
            }

            if ($multiplicationEnabled) {
                $result += intval($matches[1][$key][0]) * intval($matches[2][$key][0]);
            }

            $lastMultiplicationPosition = $match[1];
        }

        return (string) $result;
    }
}
