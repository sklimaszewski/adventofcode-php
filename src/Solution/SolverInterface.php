<?php

declare(strict_types=1);

namespace AdventOfCode\Solution;

use AdventOfCode\Exception\NotImplementedException;
use Symfony\Component\Console\Output\OutputInterface;

interface SolverInterface
{
    public function __construct(OutputInterface $output);

    /**
     * Solve first part of the puzzle for the given input file.
     */
    public function solveFirstPart(string $inputFile): string;

    /**
     * Solve second part of the puzzle for the given input file.
     *
     * @throws NotImplementedException
     */
    public function solveSecondPart(string $inputFile): string;
}
