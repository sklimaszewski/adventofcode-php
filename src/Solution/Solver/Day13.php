<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Solver;

use AdventOfCode\Solution\AbstractSolver;
use AdventOfCode\Solution\Model\Day13\Button;
use AdventOfCode\Solution\Model\Day13\Game;
use AdventOfCode\Solution\SolverInterface;

class Day13 extends AbstractSolver implements SolverInterface
{
    public function solveFirstPart(string $inputFile): string
    {
        $totalCost = 0;

        $games = $this->loadGames($inputFile);
        foreach ($games as $game) {
            try {
                [$aClicks, $bClicks] = $this->findButtonClicks(
                    $game->aButton->xMove,
                    $game->bButton->xMove,
                    $game->prizeX,
                    $game->aButton->yMove,
                    $game->bButton->yMove,
                    $game->prizeY,
                );

                $totalCost += ($game->aButton->tokenCost * $aClicks) + ($game->bButton->tokenCost * $bClicks);
            } catch (\InvalidArgumentException $e) {
                // Cannot find a solution for the given moves combination
            }
        }

        return (string) $totalCost;
    }

    public function solveSecondPart(string $inputFile): string
    {
        $totalCost = 0;

        $games = $this->loadGames($inputFile, 10000000000000);
        foreach ($games as $game) {
            try {
                [$aClicks, $bClicks] = $this->findButtonClicks(
                    $game->aButton->xMove,
                    $game->bButton->xMove,
                    $game->prizeX,
                    $game->aButton->yMove,
                    $game->bButton->yMove,
                    $game->prizeY,
                );

                $totalCost += ($game->aButton->tokenCost * $aClicks) + ($game->bButton->tokenCost * $bClicks);
            } catch (\InvalidArgumentException $e) {
                // Cannot find a solution for the given moves combination
            }
        }

        return (string) $totalCost;
    }

    /**
     * @return array{int, int}
     */
    private function findButtonClicks(int $aButtonX, int $bButtonX, int $targetX, int $aButtonY, int $bButtonY, int $targetY): array
    {
        // Find the gcd to simplify the equations
        $gcd1 = gmp_gcd($aButtonX, $bButtonX);
        $gcd2 = gmp_gcd($aButtonY, $bButtonY);

        if ($targetX % gmp_intval($gcd1) !== 0 || $targetY % gmp_intval($gcd2) !== 0) {
            throw new \InvalidArgumentException('No solution exists for the given moves combination');
        }

        // Simplify the equations
        $aButtonX /= gmp_intval($gcd1);
        $bButtonX /= gmp_intval($gcd1);
        $targetX /= gmp_intval($gcd1);

        $aButtonY /= gmp_intval($gcd2);
        $bButtonY /= gmp_intval($gcd2);
        $targetY /= gmp_intval($gcd2);

        $determinant = $aButtonX * $bButtonY - $aButtonY * $bButtonX;

        // Check for singularity (determinant should not be zero for a valid solution)
        if ($determinant === 0) {
            throw new \InvalidArgumentException('No common solution found');
        }

        // Cramer's rule FTW!
        $x = ($targetX * $bButtonY - $targetY * $bButtonX) / $determinant;
        $y = ($aButtonX * $targetY - $aButtonY * $targetX) / $determinant;

        // Check if both x and y are integers and positive
        if (is_int($x) && is_int($y) && $x > 0 && $y > 0) {
            return [$x, $y];
        }

        throw new \InvalidArgumentException('No common solution found');
    }

    /**
     * @return Game[]
     */
    private function loadGames(string $inputFile, int $offset = 0): array
    {
        $games = [];

        $aButton = null;
        $bButton = null;
        foreach ($this->readInputByLine($inputFile) as $input) {
            preg_match_all('/\-?[0-9]+/', $input, $matches);

            $values = $matches[0];
            if (!$values) {
                continue;
            }

            if (!$aButton) {
                $aButton = new Button((int) $values[0], (int) $values[1], 3);
            } elseif (!$bButton) {
                $bButton = new Button((int) $values[0], (int) $values[1], 1);
            } else {
                $games[] = new Game($aButton, $bButton, $offset + (int) $values[0], $offset + (int) $values[1]);

                $aButton = null;
                $bButton = null;
            }
        }

        return $games;
    }
}
