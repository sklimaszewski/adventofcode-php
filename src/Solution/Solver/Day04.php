<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day04 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $rows = [];
        $cols = [];
        $diag1 = [];
        $diag2 = [];

        foreach ($this->readInputByLine($inputFile) as $i => $line) {
            /** @var int $i */
            $rows[] = $line;

            foreach (str_split($line) as $j => $char) {
                if (!isset($cols[$j])) {
                    $cols[$j] = '';
                }
                $cols[$j] .= $char;

                $diag1Index = $i - $j;
                if (!isset($diag1[$diag1Index])) {
                    $diag1[$diag1Index] = '';
                }
                $diag1[$diag1Index] .= $char;

                $diag2Index = $i + $j;
                if (!isset($diag2[$diag2Index])) {
                    $diag2[$diag2Index] = '';
                }
                $diag2[$diag2Index] .= $char;
            }
        }

        $all = array_merge($rows, array_values($cols), array_values($diag1), array_values($diag2));

        $count = 0;
        foreach ($all as $line) {
            $count += substr_count($line, 'XMAS');
            $count += substr_count($line, strrev('XMAS'));
        }

        return (string) $count;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $rows = [];

        foreach ($this->readInputByLine($inputFile) as $line) {
            $row = [];
            foreach (str_split($line) as $char) {
                $row[] = $char;
            }

            $rows[] = $row;
        }

        $count = 0;
        $search = ['MAS', strrev('MAS')];

        foreach ($rows as $y => $row) {
            if ($y > 0 && $y < (count($rows) - 1)) {
                foreach ($row as $x => $col) {
                    if ($x > 0 && $x < (count($row) - 1) && $col === 'A') {
                        $diag1 = $rows[$y - 1][$x - 1] . $col . $rows[$y + 1][$x + 1];
                        $diag2 = $rows[$y - 1][$x + 1] . $col . $rows[$y + 1][$x - 1];

                        if (in_array($diag1, $search, true) && in_array($diag2, $search, true)) {
                            ++$count;
                        }
                    }
                }
            }
        }

        return (string) $count;
    }
}
