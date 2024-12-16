<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day16;

enum Direction: int
{
    case NORTH = 1;
    case SOUTH = 2;
    case WEST = 3;
    case EAST = 4;
}
