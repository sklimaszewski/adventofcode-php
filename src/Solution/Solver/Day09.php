<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day09 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $disk = $this->loadDiskMap($inputFile);

        $index = count($disk);
        while ($index--) {
            if ($disk[$index] === null) {
                // Already empty space
                continue;
            }

            foreach ($disk as $newIndex => $value) {
                if ($value === null && $index > $newIndex) {
                    $disk[$newIndex] = $disk[$index];
                    $disk[$index] = null;
                    break;
                }
            }
        }

        return $this->checksum($disk);
    }

    public function solveSecondPart(string $inputFile): string
    {
        $disk = $this->loadDiskMap($inputFile);

        $i = count($disk);

        $fileValue = $disk[$i - 1];
        $fileSize = 0;

        while ($i--) {
            if ($disk[$i] === $fileValue) {
                ++$fileSize;
            } else {
                if ($fileValue !== null) {
                    // Try to allocate file
                    $freeSpace = 0;
                    for ($j = 0; $j < count($disk); ++$j) {
                        if ($disk[$j] === null) {
                            ++$freeSpace;

                            if ($freeSpace >= $fileSize) {
                                // Move file
                                while ($fileSize > 0) {
                                    $k = ($j - $freeSpace + 1);
                                    $disk[$k] = $fileValue;
                                    $disk[$i + $fileSize] = null;

                                    --$fileSize;
                                    --$freeSpace;
                                }

                                break;
                            }
                        } else {
                            $freeSpace = 0;
                        }

                        if ($j >= $i) {
                            break;
                        }
                    }
                }

                $fileValue = $disk[$i];
                $fileSize = 1;
            }
        }

        return $this->checksum($disk);
    }

    /**
     * @return array<int, null|int>
     */
    private function loadDiskMap(string $inputFile): array
    {
        $diskMap = [];

        $fileId = 0;
        foreach (str_split($this->readInput($inputFile)) as $i => $char) {
            for ($j = 0; $j < intval($char); ++$j) {
                $diskMap[] = $i % 2 === 0 ? $fileId : null;
            }

            if ($i % 2 === 0) {
                ++$fileId;
            }
        }

        return $diskMap;
    }

    /**
     * @param array<int, null|int> $diskMap
     */
    private function checksum(array $diskMap): string
    {
        $checksum = 0;

        foreach ($diskMap as $index => $value) {
            if ($value !== null) {
                $checksum += $index * $value;
            }
        }

        return (string) $checksum;
    }
}
