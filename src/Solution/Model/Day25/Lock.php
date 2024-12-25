<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day25;

class Lock
{
    /**
     * @var int[]
     */
    private array $pins;

    public function __construct()
    {
        $this->pins = [0, 0, 0, 0, 0];
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

    public function open(Key $key): bool
    {
        foreach ($key->pins as $i => $pin) {
            if ($pin + $this->pins[$i] > 5) {
                return false;
            }
        }

        return true;
    }
}
