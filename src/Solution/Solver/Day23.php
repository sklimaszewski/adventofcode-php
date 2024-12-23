<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\SolverInterface;

class Day23 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $map = $this->loadNetworkMap($inputFile);

        $sets = [];
        foreach ($map as $n1 => $nodes) {
            foreach ($nodes as $n2) {
                foreach ($map[$n2] as $n3) {
                    if (in_array($n1, $map[$n3], true)) {
                        $set = [$n1, $n2, $n3];
                        sort($set);

                        $sets[implode('-', $set)] = $set;
                    }
                }
            }
        }

        $count = 0;
        foreach ($sets as $set) {
            foreach ($set as $n) {
                if (str_starts_with($n, 't')) {
                    ++$count;
                    break;
                }
            }
        }

        return (string) $count;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $map = $this->loadNetworkMap($inputFile);

        $clique = [];

        $nodes = array_keys($map);
        $this->bronKerbosch([], $nodes, [], $clique, $map);

        return implode(',', $clique);
    }

    /**
     * @param string[]                $r
     * @param string[]                $p
     * @param string[]                $x
     * @param string[]                $largestClique
     * @param array<string, string[]> $graph
     */
    private function bronKerbosch(array $r, array $p, array $x, array &$largestClique, array $graph): void
    {
        if (empty($p) && empty($x)) {
            // Found a maximal clique
            if (count($r) > count($largestClique)) {
                $largestClique = $r;
            }
            return;
        }

        foreach ($p as $node) {
            $neighbors = $graph[$node] ?? [];

            $this->bronKerbosch(
                array_merge($r, [$node]),
                array_intersect($p, $neighbors),
                array_intersect($x, $neighbors),
                $largestClique,
                $graph
            );

            $p = array_diff($p, [$node]);
            $x = array_merge($x, [$node]);
        }
    }

    /**
     * @return array<string, string[]>
     */
    private function loadNetworkMap(string $inputFile): array
    {
        $map = [];

        foreach ($this->readInputByLine($inputFile) as $line) {
            [$from, $to] = explode('-', $line);

            $map[$from][] = $to;
            $map[$to][] = $from;
        }

        return $map;
    }
}
