<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day07 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $answer = 0;

        foreach ($this->readInputByLine($inputFile) as $line) {
            $result = intval(explode(':', $line)[0]);
            $numbers = array_map('intval', explode(' ', trim(explode(':', $line)[1])));

            $variations = [];
            $this->generateVariants(count($numbers) - 1, [], $variations, ['+', '*']);

            for ($i = 0; $i < (2 ** (count($numbers) - 1)); ++$i) {
                $validResult = $numbers[0];

                foreach ($variations as $j => $variation) {
                    switch ($variation[$i]) {
                        case '+':
                            $validResult += $numbers[$j + 1];
                            break;
                        case '*':
                            $validResult *= $numbers[$j + 1];
                            break;
                    }
                }

                if ($validResult === $result) {
                    $answer += $result;
                    break;
                }
            }
        }

        return (string) $answer;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $answer = 0;

        foreach ($this->readInputByLine($inputFile) as $line) {
            $result = intval(explode(':', $line)[0]);
            $numbers = array_map('intval', explode(' ', trim(explode(':', $line)[1])));

            $variations = [];
            $this->generateVariants(count($numbers) - 1, [], $variations, ['+', '*', '||']);

            for ($i = 0; $i < (3 ** (count($numbers) - 1)); ++$i) {
                $previousValue = $numbers[0];

                foreach ($variations as $j => $variation) {
                    switch ($variation[$i]) {
                        case '+':
                            $previousValue += $numbers[$j + 1];
                            break;
                        case '*':
                            $previousValue *= $numbers[$j + 1];
                            break;
                        case '||':
                            $previousValue = intval($previousValue . '' . $numbers[$j + 1]);
                            break;
                    }
                }

                if ($previousValue === $result) {
                    $answer += $result;
                    break;
                }
            }
        }

        return (string) $answer;
    }

    /**
     * @param string[]   $variant
     * @param string[][] $variants
     * @param string[]   $operators
     */
    private function generateVariants(int $depth, array $variant, array &$variants, array $operators): void
    {
        if ($depth === 0) {
            foreach ($variant as $key => $variation) {
                $variants[$key][] = $variation;
            }

            return;
        }

        foreach ($operators as $operator) {
            $this->generateVariants($depth - 1, [...$variant, $operator], $variants, $operators);
        }
    }
}
