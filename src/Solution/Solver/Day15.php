<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\Model\Day15\Box;
use AdventOfCode\Solution\Model\Day15\Direction;
use AdventOfCode\Solution\Model\Day15\Map;
use AdventOfCode\Solution\Model\Day15\Robot;
use AdventOfCode\Solution\Model\Day15\Wall;
use AdventOfCode\Solution\Model\Day15\WideBox;
use AdventOfCode\Solution\SolverInterface;

class Day15 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        [$map, $movements] = $this->loadInput($inputFile);

        $robot = $map->getRobot();
        foreach ($movements as $movement) {
            $robot->move($movement);
        }

        return (string) $map->sumGPS();
    }

    public function solveSecondPart(string $inputFile): string
    {
        [$map, $movements] = $this->loadInput($inputFile, true);

        $robot = $map->getRobot();
        foreach ($movements as $movement) {
            $robot->move($movement);
        }

        return (string) $map->sumGPS();
    }

    /**
     * @return array{Map, Direction[]}
     */
    private function loadInput(string $inputFile, bool $doubleWidth = false): array
    {
        $map = new Map();
        $movements = [];
        $movementsInput = false;

        $y = 0;
        foreach ($this->readInputByLine($inputFile) as $line) {
            if (!$movementsInput) {
                $x = 0;

                foreach (str_split($line) as $char) {
                    if ($char === '#') {
                        $map->addItem(new Wall($map, $x, $y));
                        if ($doubleWidth) {
                            ++$x;
                            $map->addItem(new Wall($map, $x, $y));
                        }
                    } elseif ($char === 'O') {
                        if ($doubleWidth) {
                            $map->addItem(new WideBox($map, $x, $y));
                            ++$x;
                        } else {
                            $map->addItem(new Box($map, $x, $y));
                        }
                    } elseif ($char === '@') {
                        $map->addItem(new Robot($map, $x, $y));
                        if ($doubleWidth) {
                            ++$x;
                        }
                    } elseif ($char === '.') {
                        if ($doubleWidth) {
                            ++$x;
                        }
                    }

                    ++$x;
                }

                ++$y;
            } else {
                foreach (str_split($line) as $char) {
                    switch ($char) {
                        case '^':
                            $movements[] = Direction::NORTH;
                            break;
                        case 'v':
                            $movements[] = Direction::SOUTH;
                            break;
                        case '<':
                            $movements[] = Direction::WEST;
                            break;
                        case '>':
                            $movements[] = Direction::EAST;
                            break;
                    }
                }
            }

            if (!$line) {
                $movementsInput = true;
            }
        }

        return [$map, $movements];
    }
}
