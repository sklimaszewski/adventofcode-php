<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\Model\Day14\Robot;
use AdventOfCode\Solution\SolverInterface;

class Day14 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $robots = $this->loadRobots($inputFile);

        $ticks = 100;

        $mapX = 101;
        $mapY = 103;

        foreach ($robots as $robot) {
            $robot->x = ($robot->x + $robot->xMove * $ticks) % $mapX;
            if ($robot->x < 0) {
                $robot->x += $mapX;
            }

            $robot->y = ($robot->y + $robot->yMove * $ticks) % $mapY;
            if ($robot->y < 0) {
                $robot->y += $mapY;
            }
        }

        $quadrants = [0, 0, 0, 0];
        foreach ($robots as $robot) {
            $xAxis = floor(($mapX - 1) / 2);
            $yAxis = floor(($mapY - 1) / 2);

            if ($robot->x < $xAxis && $robot->y < $yAxis) {
                ++$quadrants[0];
            } elseif ($robot->x > $xAxis && $robot->y < $yAxis) {
                ++$quadrants[1];
            } elseif ($robot->x < $xAxis && $robot->y > $yAxis) {
                ++$quadrants[2];
            } elseif ($robot->x > $xAxis && $robot->y > $yAxis) {
                ++$quadrants[3];
            }
        }

        $total = 1;
        foreach ($quadrants as $count) {
            $total *= $count;
        }

        return (string) $total;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $robots = $this->loadRobots($inputFile);

        $mapX = 101;
        $mapY = 103;

        $tick = 1;
        while (true) {
            // Recalculate robots positions
            foreach ($robots as $robot) {
                $robot->x = ($robot->x + $robot->xMove) % $mapX;
                if ($robot->x < 0) {
                    $robot->x += $mapX;
                }

                $robot->y = ($robot->y + $robot->yMove) % $mapY;
                if ($robot->y < 0) {
                    $robot->y += $mapY;
                }
            }

            // Build robots map
            $map = [];
            foreach ($robots as $robot) {
                $map[$robot->y][$robot->x] = true;
            }

            // Check if we found a Christmas tree, output if verbose
            if ($this->isChristmasTree($map, $mapX, $mapY)) {
                if ($this->output->isVerbose()) {
                    $this->outputRobotsPositions($map, $mapX, $mapY);
                }

                return (string) $tick;
            }

            ++$tick;
        }
    }

    /**
     * To find a Christmas tree we need to find at least 10 robots in a row, one by one.
     *
     * @param array<int, array<int, bool>> $map
     */
    private function isChristmasTree(array $map, int $mapX, int $mapY): bool
    {
        for ($y = 0; $y < $mapY; ++$y) {
            $singleRowRobots = 0;

            for ($x = 0; $x < $mapX; ++$x) {
                if ($map[$y][$x] ?? false) {
                    ++$singleRowRobots;
                } else {
                    $singleRowRobots = 0;
                }

                if ($singleRowRobots >= 10) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array<int, array<int, bool>> $map
     */
    private function outputRobotsPositions(array $map, int $mapX, int $mapY): void
    {
        for ($y = 0; $y < $mapY; ++$y) {
            for ($x = 0; $x < $mapX; ++$x) {
                if ($map[$y][$x] ?? false) {
                    $this->output->write('*');
                } else {
                    $this->output->write(' ');
                }
            }

            $this->output->writeln('');
        }
    }

    /**
     * @return Robot[]
     */
    private function loadRobots(string $inputFile): array
    {
        $robots = [];

        foreach ($this->readInputByLine($inputFile) as $line) {
            preg_match_all('/-?\d+/', $line, $matches);

            $robots[] = new Robot(
                intval($matches[0][0]),
                intval($matches[0][1]),
                intval($matches[0][2]),
                intval($matches[0][3]),
            );
        }

        return $robots;
    }
}
