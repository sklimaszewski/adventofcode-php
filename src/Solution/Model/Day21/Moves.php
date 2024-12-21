<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day21;

class Moves
{
    /**
     * @var array<string, array<string, string[]>>
     */
    private array $moves;

    public function __construct()
    {
        $numericKeypad = array_merge(...array_map([$this, 'mapButton'], ['789', '456', '123', ' 0A'], array_keys(['789', '456', '123', ' 0A'])));
        $arrowKeypad = array_merge(...array_map([$this, 'mapButton'], [' ^A', '<v>'], array_keys([' ^A', '<v>'])));

        $this->moves = [];
        foreach ($numericKeypad as $fromPair) {
            [$a, $from] = $fromPair;
            foreach ($numericKeypad as $toPair) {
                [$b, $to] = $toPair;
                $this->moves[$a][$b] = iterator_to_array(self::findMoves($from, $to, self::getKeyPosition($numericKeypad, ' ')));
            }
        }

        foreach ($arrowKeypad as $fromPair) {
            [$a, $from] = $fromPair;
            foreach ($arrowKeypad as $toPair) {
                [$b, $to] = $toPair;
                $this->moves[$a][$b] = iterator_to_array(self::findMoves($from, $to, self::getKeyPosition($arrowKeypad, ' ')));
            }
        }
    }

    /**
     * @return string[]
     */
    public function get(string $from, string $to): array
    {
        return $this->moves[$from][$to];
    }

    /**
     * @return array<int, array{0: string, 1: array{0: int, 1: int}}>
     */
    private function mapButton(string $row, int $i): array
    {
        return array_map(static fn ($char, $x) => [$char, [$x, $i]], str_split($row), array_keys(str_split($row)));
    }

    /**
     * @param array{int, int}      $from
     * @param array{int, int}      $to
     * @param null|array{int, int} $avoid
     *
     * @return iterable<string>
     */
    private static function findMoves(array $from, array $to, ?array $avoid): iterable
    {
        [$fromX, $fromY] = $from;
        [$toX, $toY] = $to;
        if ($avoid === null) {
            $avoidX = null;
            $avoidY = null;
        } else {
            [$avoidX, $avoidY] = $avoid;
        }

        if (($fromX === $avoidX && $fromY === $avoidY) || ($toX === $avoidX && $toY === $avoidY)) {
            return;
        }

        if ($fromX === $toX && $fromY === $toY) {
            yield 'A';
            return;
        }

        if ($fromX < $toX) {
            foreach (self::findMoves([$fromX + 1, $fromY], $to, $avoid) as $move) {
                yield '>' . $move;
            }
        }

        if ($fromX > $toX) {
            foreach (self::findMoves([$fromX - 1, $fromY], $to, $avoid) as $move) {
                yield '<' . $move;
            }
        }

        if ($fromY < $toY) {
            foreach (self::findMoves([$fromX, $fromY + 1], $to, $avoid) as $move) {
                yield 'v' . $move;
            }
        }

        if ($fromY > $toY) {
            foreach (self::findMoves([$fromX, $fromY - 1], $to, $avoid) as $move) {
                yield '^' . $move;
            }
        }
    }

    /**
     * @param array<int, array{0: string, 1: array{0: int, 1: int}}> $keypad
     *
     * @return null|array{0: int, 1: int}
     */
    private static function getKeyPosition(array $keypad, string $key): ?array
    {
        foreach ($keypad as $pair) {
            if ($pair[0] === $key) {
                return $pair[1];
            }
        }

        return null;
    }
}
