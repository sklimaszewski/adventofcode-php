<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day24;

enum Operation: string
{
    case AND = 'AND';
    case OR = 'OR';
    case XOR = 'XOR';
}
