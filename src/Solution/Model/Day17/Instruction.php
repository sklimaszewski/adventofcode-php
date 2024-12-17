<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day17;

enum Instruction: int
{
    case ADV = 0;
    case BXL = 1;
    case BST = 2;
    case JNZ = 3;
    case BXC = 4;
    case OUT = 5;
    case BDV = 6;
    case CDV = 7;
}
