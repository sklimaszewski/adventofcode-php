<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day20;

class Map
{
    /**
     * @var array<int, array<int, bool>>
     */
    private array $tiles = [];

    private ?int $startX = null;

    private ?int $startY = null;

    private ?int $endX = null;

    private ?int $endY = null;

    public function addTile(int $x, int $y, bool $walkable): void
    {
        $this->tiles[$x][$y] = $walkable;
    }

    public function setStart(int $x, int $y): void
    {
        $this->startX = $x;
        $this->startY = $y;
    }

    public function setEnd(int $x, int $y): void
    {
        $this->endX = $x;
        $this->endY = $y;
    }

    public function getTrack(): Position
    {
        if ($this->startX === null || $this->startY === null || $this->endX === null || $this->endY === null) {
            throw new \Exception('Start or end position not set.');
        }

        $x = $this->startX;
        $y = $this->startY;

        $position = null;

        while ($x !== $this->endX || $y !== $this->endY) {
            $position = new Position(
                $x,
                $y,
                $position ? $position->steps + 1 : 0,
                0,
                $position,
            );

            $possibleNextTiles = [
                [$x, $y - 1],
                [$x + 1, $y],
                [$x, $y + 1],
                [$x - 1, $y],
            ];

            foreach ($possibleNextTiles as [$nextX, $nextY]) {
                if (($this->tiles[$nextX][$nextY] ?? false) && !$position->hasVisited($nextX, $nextY)) {
                    $x = $nextX;
                    $y = $nextY;

                    break;
                }
            }
        }

        return new Position(
            $x,
            $y,
            $position ? $position->steps + 1 : 0,
            0,
            $position,
        );
    }

    public function countCheatRoutes(int $minimumCheatSaveSteps = 1, int $maximumCheatSteps = 2): int
    {
        if ($this->startX === null || $this->startY === null || $this->endX === null || $this->endY === null) {
            throw new \Exception('Start or end position not set.');
        }

        $track = $this->getTrack();
        $positions = $track->getPositions();

        $stepsLeftMap = [];
        foreach ($positions as $i => [$x, $y]) {
            $stepsLeftMap[$x][$y] = $track->steps - $i;
        }

        $map = [];
        $count = 0;
        foreach ($positions as [$x, $y]) {
            $stepsLeft = $stepsLeftMap[$x][$y];

            $possibleCheatShortcuts = $this->getPossiblePositions($x, $y, $maximumCheatSteps);
            foreach ($possibleCheatShortcuts as [$nextX, $nextY]) {
                $cheatStepsLeft = $stepsLeftMap[$nextX][$nextY] ?? null;
                if ($cheatStepsLeft !== null) {
                    $stepsSaved = $stepsLeft - ($this->getDistance($x, $y, $nextX, $nextY) + $cheatStepsLeft);
                    if ($stepsSaved >= $minimumCheatSaveSteps) {
                        ++$count;

                        $map[$stepsSaved] ??= 0;
                        ++$map[$stepsSaved];
                    }
                }
            }
        }

        return $count;
    }

    /**
     * @return array<int, array{int, int}>
     */
    private function getPossiblePositions(int $x, int $y, int $range): array
    {
        $positions = [];

        for ($dx = -$range; $dx <= $range; ++$dx) {
            for ($dy = -$range; $dy <= $range; ++$dy) {
                if ((abs($dx) + abs($dy) <= $range) && !($dx === 0 && $dy === 0)) {
                    $positions[] = [$x + $dx, $y + $dy];
                }
            }
        }

        return $positions;
    }

    private function getDistance(int $fromX, int $fromY, int $toX, int $toY): int
    {
        return abs($fromX - $toX) + abs($fromY - $toY);
    }
}
