<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day15;

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
