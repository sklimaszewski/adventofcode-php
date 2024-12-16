<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day15;

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
