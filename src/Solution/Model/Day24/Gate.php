<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day24;

class Gate
{
    public function __construct(
        public string $input1,
        public string $input2,
        public Operation $operation,
        public string $output
    ) {
    }

    public function getResult(int $value1, int $value2): int
    {
        switch ($this->operation) {
            case Operation::AND:
                return $value1 & $value2;
            case Operation::OR:
                return $value1 | $value2;
            case Operation::XOR:
                return $value1 ^ $value2;
        }
    }
}
