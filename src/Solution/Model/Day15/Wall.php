<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day15;

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
