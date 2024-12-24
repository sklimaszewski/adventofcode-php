<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\Model\Day24\Gate;
use AdventOfCode\Solution\Model\Day24\Operation;
use AdventOfCode\Solution\SolverInterface;

class Day24 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        [$values, $gates] = $this->loadInput($inputFile);

        while (count($gates) > 0) {
            foreach ($gates as $key => $gate) {
                if (array_key_exists($gate->input1, $values)) {
                    $value1 = $values[$gate->input1];
                } else {
                    continue;
                }

                if (array_key_exists($gate->input2, $values)) {
                    $value2 = $values[$gate->input2];
                } else {
                    continue;
                }

                $values[$gate->output] = $gate->getResult($value1, $value2);
                unset($gates[$key]);
            }
        }

        ksort($values);

        $binary = '';
        foreach ($values as $wire => $value) {
            if (str_starts_with($wire, 'z')) {
                $binary = $value . $binary;
            }
        }

        return (string) bindec($binary);
    }

    public function solveSecondPart(string $inputFile): string
    {
        [$values, $gates] = $this->loadInput($inputFile);

        $swapValues = [];
        $bitCount = intval(count($values) / 2);

        $carry = null;
        for ($i = 0; $i < $bitCount; ++$i) {
            $x = sprintf('x%02d', $i);
            $y = sprintf('y%02d', $i);
            $z = sprintf('z%02d', $i);

            // Sum
            if ($carry) {
                $sum = $this->findGate($gates, $x, $y, Operation::XOR)->output;
                try {
                    $this->findGate($gates, $sum, $carry, Operation::XOR, $z);
                } catch (\Exception $e) {
                    try {
                        // Result of XOR is not correct, should be $z
                        $gate = $this->findGate($gates, $sum, $carry, Operation::XOR);
                        $swapGate = $this->findGateByOutput($gates, $z);

                        $swapValues[] = $gate->output;
                        $swapValues[] = $swapGate->output;

                        $swapGate->output = $gate->output;
                        $gate->output = $z;
                    } catch (\Exception $e) {
                        // Result of XOR is not correct
                        $gate = $this->findGate($gates, $x, $y, Operation::XOR);

                        // Find correct value for above gate, by finding correct sum
                        $prevGate = $this->findGate($gates, null, $carry, Operation::XOR, $z);
                        $sum = $prevGate->input1 === $carry ? $prevGate->input2 : $prevGate->input1;
                        $swapGate = $this->findGateByOutput($gates, $sum);

                        $swapValues[] = $gate->output;
                        $swapValues[] = $swapGate->output;

                        $swapGate->output = $gate->output;
                        $gate->output = $sum;
                    }
                }
            } else {
                $this->findGate($gates, $x, $y, Operation::XOR, $z);
            }

            // Carry
            $v1 = $this->findGate($gates, $x, $y, Operation::AND)->output;
            if ($carry) {
                $v2 = $this->findGate($gates, $carry, $this->findGate($gates, $x, $y, Operation::XOR)->output, Operation::AND)->output;
                $carry = $this->findGate($gates, $v1, $v2, Operation::OR)->output;
            } else {
                $carry = $this->findGate($gates, $x, $y, Operation::AND)->output;
            }
        }

        sort($swapValues);

        return implode(',', $swapValues);
    }

    /**
     * @param Gate[] $gates
     *
     * @throws \Exception
     */
    private function findGate(array $gates, ?string $input1, ?string $input2, ?Operation $operation, ?string $output = null): Gate
    {
        foreach ($gates as $gate) {
            if ($operation !== null && $gate->operation !== $operation) {
                continue;
            }

            if ($output !== null && $gate->output !== $output) {
                continue;
            }

            if ($input1 !== null && $gate->input1 !== $input1 && $gate->input2 !== $input1) {
                continue;
            }

            if ($input2 !== null && $gate->input1 !== $input2 && $gate->input2 !== $input2) {
                continue;
            }

            return $gate;
        }

        throw new \Exception(sprintf('Gate not found for %s %s %s => %s', $input1, $operation?->value, $input2, $output));
    }

    /**
     * @param Gate[] $gates
     *
     * @throws \Exception
     */
    private function findGateByOutput(array $gates, string $output): Gate
    {
        foreach ($gates as $gate) {
            if ($gate->output === $output) {
                return $gate;
            }
        }

        throw new \Exception(sprintf('Gate not found for %s', $output));
    }

    /**
     * @return array{array<string, int>, Gate[]}
     */
    private function loadInput(string $inputFile): array
    {
        $values = [];
        $gates = [];

        $loadingValues = true;
        foreach ($this->readInputByLine($inputFile) as $line) {
            if (!$line) {
                $loadingValues = false;
            } elseif ($loadingValues) {
                [$gate, $value] = explode(': ', $line);
                $values[$gate] = intval($value);
            } else {
                [$gate, $output] = explode(' -> ', $line);
                [$input1, $operation, $input2] = explode(' ', $gate);

                $gates[] = new Gate(
                    $input1,
                    $input2,
                    Operation::from($operation),
                    $output
                );
            }
        }

        return [$values, $gates];
    }
}
