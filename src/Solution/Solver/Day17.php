<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\Model\Day17\Computer;
use AdventOfCode\Solution\SolverInterface;

class Day17 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $computer = $this->loadInput($inputFile);

        return $computer->run();
    }

    public function solveSecondPart(string $inputFile): string
    {
        $computer = $this->loadInput($inputFile);

        $i = (2 ** (count($computer->instructions) - 1)) ** 3;

        $correctNumbers = 0;
        $summand = intval($i / 100);

        while (true) {
            $copy = clone $computer;
            $copy->registerA = $i;

            $input = $copy->getInput();
            $output = $copy->run();

            if ($input === $output) {
                return (string) $i;
            }

            $inputValues = array_map('intval', explode(',', $input));
            $outputValues = array_map('intval', explode(',', $output));

            $validNumbers = $this->countCorrectNumberFromEnd($inputValues, $outputValues);
            if ($correctNumbers !== $validNumbers) {
                $correctNumbers = $validNumbers;
                $summand = max(intval($summand / 10), 1);
            }

            $i += $summand;
        }
    }

    /**
     * @param int[] $original
     * @param int[] $compare
     */
    private function countCorrectNumberFromEnd(array $original, array $compare): int
    {
        $count = 0;
        $original = array_reverse($original);
        $compare = array_reverse($compare);

        foreach ($original as $key => $value) {
            if ($value === $compare[$key]) {
                ++$count;
            } else {
                break;
            }
        }

        return $count;
    }

    private function loadInput(string $inputFile): Computer
    {
        $registerA = 0;
        $registerB = 0;
        $registerC = 0;

        $instructions = [];

        foreach ($this->readInputByLine($inputFile) as $line) {
            if (str_contains($line, 'Register A')) {
                $registerA = (int) str_replace('Register A: ', '', $line);
                continue;
            }
            if (str_contains($line, 'Register B')) {
                $registerB = (int) str_replace('Register B: ', '', $line);
                continue;
            }
            if (str_contains($line, 'Register C')) {
                $registerC = (int) str_replace('Register C: ', '', $line);
                continue;
            }
            if (str_contains($line, 'Program')) {
                $instructions = array_map('intval', explode(',', str_replace('Program: ', '', $line)));
            }
        }

        return new Computer(
            $registerA,
            $registerB,
            $registerC,
            $instructions
        );
    }
}
