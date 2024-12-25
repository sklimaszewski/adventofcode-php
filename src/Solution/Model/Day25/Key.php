<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day25;

class Key
{
    /**
     * @var int[]
     */
    public array $pins;

    public function __construct()
    {
        // Compensate that last pin is not used
        $this->pins = [-1, -1, -1, -1, -1];
    }

    /**
     * @param int[] $pins
     */
    public function addPins(array $pins): void
    {
        foreach ($pins as $i => $pin) {
            $this->pins[$i] += $pin;
        }
    }
}
