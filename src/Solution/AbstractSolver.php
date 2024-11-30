<?php

declare(strict_types=1);

namespace AdventOfCode\Solution;

use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractSolver implements SolverInterface
{
    public function __construct(
        protected OutputInterface $output
    ) {
    }

    /**
     * Generator function for reading input file line by line to reduce memory usage.
     */
    protected function readInputByLine(string $inputFile): iterable
    {
        $handle = fopen($inputFile, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                yield $line;
            }

            fclose($handle);
        }
    }

    /**
     * Read whole input file as a string.
     */
    protected function readInput(string $inputFile): string
    {
        return file_get_contents($inputFile);
    }
}
