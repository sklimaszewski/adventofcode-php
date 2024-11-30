<?php

declare(strict_types=1);

namespace AdventOfCode\Solution;

use Symfony\Component\Console\Output\OutputInterface;

interface SolverInterface
{
    public function __construct(OutputInterface $output);

    /**
     * Solve the puzzle for the given input file.
     */
    public function solve(string $inputFile): string;
}
