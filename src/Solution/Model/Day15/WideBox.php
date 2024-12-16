<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day15;

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
