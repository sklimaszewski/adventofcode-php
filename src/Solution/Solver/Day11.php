<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day11 extends AbstractSolver implements SolverInterface
{
    /**
     * @var array<int, array<int, int>>
     */
    private static array $cache = [];

    public function solveFirstPart(string $inputFile): string
    {
        $stones = $this->parseIntValues($this->readInput($inputFile));

        $blinksLeft = 25;
        $count = count($stones);

        foreach ($stones as $stone) {
            $this->processStone($stone, $blinksLeft, $count);
        }

        return (string) $count;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $stones = $this->parseIntValues($this->readInput($inputFile));

        $blinksLeft = 75;
        $count = count($stones);

        foreach ($stones as $stone) {
            // Using recursive function is slower but uses less memory
            $this->processStone($stone, $blinksLeft, $count);
        }

        return (string) $count;
    }

    private function processStone(int $stone, int $blinksLeft, int &$count): void
    {
        --$blinksLeft;
        if ($blinksLeft < 0) {
            return;
        }

        // Introduce cache to avoid recalculating the same values
        if (isset(self::$cache[$stone][$blinksLeft])) {
            $count += self::$cache[$stone][$blinksLeft];
            return;
        }

        $preCount = $count;

        if ($stone === 0) {
            $this->processStone(1, $blinksLeft, $count);
        } elseif (strlen((string) $stone) % 2 === 0) {
            $split = str_split((string) $stone, max(1, (int) (strlen((string) $stone) / 2)));

            $this->processStone((int) $split[0], $blinksLeft, $count);
            $this->processStone((int) $split[1], $blinksLeft, $count);

            ++$count;
        } else {
            $this->processStone($stone * 2024, $blinksLeft, $count);
        }

        $postCount = $count;
        self::$cache[$stone][$blinksLeft] = $postCount - $preCount;
    }
}
