<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day19 extends AbstractSolver implements SolverInterface
{
    /**
     * @var array<string, int>
     */
    private static array $cache = [];

    public function solveFirstPart(string $inputFile): string
    {
        [$patterns, $designs] = $this->loadInput($inputFile);

        $validDesigns = 0;
        foreach ($designs as $design) {
            if ($this->isDesignPossible($design, $patterns)) {
                ++$validDesigns;
            }
        }

        return (string) $validDesigns;
    }

    public function solveSecondPart(string $inputFile): string
    {
        [$patterns, $designs] = $this->loadInput($inputFile);

        $possibleVariations = 0;
        foreach ($designs as $i => $design) {
            $possibleVariations += $this->countDesignVariations($design, $patterns);
        }

        return (string) $possibleVariations;
    }

    /**
     * @return array{0: string[], 1: string[]}
     */
    private function loadInput(string $inputFile): array
    {
        $patterns = [];
        $designs = [];

        foreach ($this->readInputByLine($inputFile) as $line) {
            if (empty($line)) {
                continue;
            }

            if (str_contains($line, ',')) {
                $patterns = explode(', ', $line);
            } else {
                $designs[] = $line;
            }
        }

        return [$patterns, $designs];
    }

    /**
     * @param string[] $patterns
     */
    private function isDesignPossible(string $design, array $patterns): bool
    {
        if (in_array($design, $patterns, true)) {
            return true;
        }

        foreach ($patterns as $pattern) {
            if (str_starts_with($design, $pattern)) {
                $suffix = substr($design, strlen($pattern));
                if ($this->isDesignPossible($suffix, $patterns)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string[] $patterns
     */
    private function countDesignVariations(string $design, array $patterns): int
    {
        if (isset(self::$cache[$design])) {
            return self::$cache[$design];
        }

        $count = 0;

        if (in_array($design, $patterns, true)) {
            ++$count;
        }

        foreach ($patterns as $pattern) {
            if (str_starts_with($design, $pattern) && $design !== $pattern) {
                $suffix = substr($design, strlen($pattern));
                $count += $this->countDesignVariations($suffix, $patterns);
            }
        }

        self::$cache[$design] = $count;

        return $count;
    }
}
