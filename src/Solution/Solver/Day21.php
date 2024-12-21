<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\Model\Day21\Moves;
use AdventOfCode\Solution\SolverInterface;
use AdventOfCode\Solution\Traits\Memo;
use Symfony\Component\Console\Output\OutputInterface;

class Day21 extends AbstractSolver implements SolverInterface
{
    use Memo;

    private Moves $moves;

    public function __construct(OutputInterface $output)
    {
        $this->moves = new Moves();
        parent::__construct($output);
    }

    public function solveFirstPart(string $inputFile): string
    {
        $codes = $this->loadCodes($inputFile);
        $complexity = $this->getComplexity($codes);

        return (string) $complexity;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $codes = $this->loadCodes($inputFile);
        $complexity = $this->getComplexity($codes, 25);

        return (string) $complexity;
    }

    /**
     * @param string[] $codes
     */
    private function getComplexity(array $codes, int $robots = 2): int
    {
        $complexity = 0;

        foreach ($codes as $code) {
            $length = $this->getInputLength($code, $robots + 1);

            $multiplier = intval(preg_replace('/[^0-9]/', '', $code));
            $complexity += $multiplier * $length;
        }

        return $complexity;
    }

    private function getInputLength(string $code, int $robots): int
    {
        return $this->memo(function () use ($code, $robots) {
            if ($robots === 0) {
                return strlen($code);
            }

            $length = 0;
            $from = 'A';

            foreach (str_split($code) as $to) {
                /** @var non-empty-array<int> $possibleLengths */
                $possibleLengths = array_map(fn ($move) => $this->getInputLength($move, $robots - 1), $this->moves->get($from, $to));
                $length += min($possibleLengths);
                $from = $to;
            }

            return $length;
        }, [$code, (string) $robots]);
    }

    /**
     * @return string[]
     */
    private function loadCodes(string $inputFile): array
    {
        return iterator_to_array($this->readInputByLine($inputFile));
    }
}
