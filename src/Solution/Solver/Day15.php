<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

enum Direction: int
{
    case NORTH = 1;
    case SOUTH = 2;
    case WEST = 3;
    case EAST = 4;
}

class Map
{
    /**
     * @param Item[] $items
     */
    public function __construct(
        public array $items = [],
    ) {
    }

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    public function getItem(int $x, int $y): ?Item
    {
        foreach ($this->items as $item) {
            if ($item->isPosition($x, $y)) {
                return $item;
            }
        }

        return null;
    }

    public function getRobot(): Robot
    {
        foreach ($this->items as $item) {
            if ($item instanceof Robot) {
                return $item;
            }
        }

        throw new \Exception('Robot not found');
    }

    public function sumGPS(): int
    {
        $sum = 0;

        foreach ($this->items as $item) {
            if ($item instanceof Box) {
                $sum += 100 * $item->y + $item->x;
            }
        }

        return $sum;
    }
}

class Item
{
    public function __construct(
        public readonly Map $map,
        public int $x,
        public int $y,
    ) {
    }

    public function isPosition(int $x, int $y): bool
    {
        return $this->x === $x && $this->y === $y;
    }

    public function canMove(Direction $direction): bool
    {
        $nextX = $this->x;
        $nextY = $this->y;

        if ($direction === Direction::NORTH) {
            --$nextY;
        } elseif ($direction === Direction::SOUTH) {
            ++$nextY;
        } elseif ($direction === Direction::WEST) {
            --$nextX;
        } elseif ($direction === Direction::EAST) {
            ++$nextX;
        }

        $item = $this->map->getItem($nextX, $nextY);

        return !$item || $item->canMove($direction);
    }

    public function move(Direction $direction): bool
    {
        $nextX = $this->x;
        $nextY = $this->y;

        if ($direction === Direction::NORTH) {
            --$nextY;
        } elseif ($direction === Direction::SOUTH) {
            ++$nextY;
        } elseif ($direction === Direction::WEST) {
            --$nextX;
        } elseif ($direction === Direction::EAST) {
            ++$nextX;
        }

        $item = $this->map->getItem($nextX, $nextY);
        if (!$item || $item->move($direction)) {
            $this->x = $nextX;
            $this->y = $nextY;

            return true;
        }

        return false;
    }
}

class Wall extends Item
{
    public function canMove(Direction $direction): bool
    {
        return false;
    }

    public function move(Direction $direction): bool
    {
        return false;
    }
}

class Box extends Item
{
}

class WideBox extends Box
{
    public function isPosition(int $x, int $y): bool
    {
        return $this->y === $y && ($this->x === $x || $this->x + 1 === $x);
    }

    public function canMove(Direction $direction): bool
    {
        $nextX = $this->x;
        $nextY = $this->y;

        if ($direction === Direction::NORTH) {
            --$nextY;
        } elseif ($direction === Direction::SOUTH) {
            ++$nextY;
        } elseif ($direction === Direction::WEST) {
            --$nextX;
        } elseif ($direction === Direction::EAST) {
            ++$nextX;
        }

        $item = $this->map->getItem($nextX, $nextY);
        if ($item === $this) {
            $item = null;
        }

        $item2 = $this->map->getItem($nextX + 1, $nextY);
        if ($item2 === $this) {
            $item2 = null;
        }

        return (!$item || $item->canMove($direction)) && (!$item2 || $item2->canMove($direction));
    }

    public function move(Direction $direction): bool
    {
        $nextX = $this->x;
        $nextY = $this->y;

        if ($direction === Direction::NORTH) {
            --$nextY;
        } elseif ($direction === Direction::SOUTH) {
            ++$nextY;
        } elseif ($direction === Direction::WEST) {
            --$nextX;
        } elseif ($direction === Direction::EAST) {
            ++$nextX;
        }

        $item = $this->map->getItem($nextX, $nextY);
        if ($item === $this) {
            $item = null;
        }

        $item2 = $this->map->getItem($nextX + 1, $nextY);
        if ($item2 === $this) {
            $item2 = null;
        }

        if ((!$item || $item->canMove($direction)) && (!$item2 || $item2->canMove($direction))) {
            $item?->move($direction);
            if ($item !== $item2) {
                $item2?->move($direction);
            }

            $this->x = $nextX;
            $this->y = $nextY;

            return true;
        }

        return false;
    }
}

class Robot extends Item
{
}

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
