<?php

declare(strict_types=1);

namespace AdventOfCode\Solution;

use AdventOfCode\Exception\NotImplementedException;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractSolver implements SolverInterface
{
    public function __construct(
        protected OutputInterface $output
    ) {
    }

    public function solveSecondPart(string $inputFile): string
    {
        throw new NotImplementedException();
    }

    /**
     * Generator function for reading input file line by line to reduce memory usage.
     *
     * @return iterable<string>
     */
    protected function readInputByLine(string $inputFile): iterable
    {
        $handle = fopen($inputFile, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                yield trim($line);
            }

            fclose($handle);
        }
    }

    /**
     * Read whole input file as a string.
     */
    protected function readInput(string $inputFile): string
    {
        $input = file_get_contents($inputFile);
        if ($input === false) {
            throw new \RuntimeException('Failed to read input file');
        }

        return $input;
    }

    /**
     * @return int[]
     */
    protected function parseIntValues(string $input, string $separator = ' '): array
    {
        return array_map('intval', explode($separator ?: ' ', $input));
    }
}
