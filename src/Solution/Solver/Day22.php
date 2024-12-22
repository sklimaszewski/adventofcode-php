<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day22 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $secrets = $this->parseIntValues($this->readInput($inputFile), "\n");

        $sum = 0;
        foreach ($secrets as $secret) {
            $steps = 2000;
            for ($i = 0; $i < $steps; ++$i) {
                $secret = $this->evolve($secret);
            }

            $sum += $secret;
        }

        return (string) $sum;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $secrets = $this->parseIntValues($this->readInput($inputFile), "\n");

        /** @var non-empty-array<int> $patterns */
        $patterns = [];
        foreach ($secrets as $secret) {
            foreach ($this->getPatterns($secret, 2000) as $pattern => $value) {
                $patterns[$pattern] = ($patterns[$pattern] ?? 0) + $value;
            }
        }

        return (string) max($patterns);
    }

    /**
     * @return array<string, int>
     */
    private function getPatterns(int $secret, int $steps): array
    {
        $patterns = [];

        $priceChanges = [];
        $previousPrice = $secret % 10;

        for ($i = 0; $i < $steps; ++$i) {
            $secret = $this->evolve($secret);
            $price = $secret % 10;

            $priceChanges[] = $price - $previousPrice;

            if ($i >= 3) {
                $key = implode(';', array_slice($priceChanges, -4));
                if (!isset($patterns[$key])) {
                    $patterns[$key] = $price;
                }
            }

            $previousPrice = $price;
        }

        return $patterns;
    }

    private function evolve(int $secret): int
    {
        $secret ^= ($secret * 64);
        $secret %= 16777216;

        $secret ^= (int) floor($secret / 32);
        $secret %= 16777216;

        $secret ^= ($secret * 2048);
        $secret %= 16777216;

        return $secret;
    }
}
