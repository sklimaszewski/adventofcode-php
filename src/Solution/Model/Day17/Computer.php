<?php

declare(strict_types=1);

namespace AdventOfCode\Solution\Model\Day17;

class Computer
{
    private int $pointer = 0;

    /**
     * @param int[] $instructions
     */
    public function __construct(
        public int $registerA,
        public int $registerB,
        public int $registerC,
        public array $instructions,
    ) {
    }

    public function getInput(): string
    {
        return implode(',', $this->instructions);
    }

    public function run(): string
    {
        $output = [];

        while (true) {
            $instruction = $this->instructions[$this->pointer] ?? null;
            $operand = $this->instructions[$this->pointer + 1] ?? null;

            if ($instruction === null || $operand === null) {
                break;
            }

            $this->pointer += 2;

            switch (Instruction::from($instruction)) {
                case Instruction::ADV:
                    $value = $this->loadValue($operand);
                    $this->registerA = intval($this->registerA / (2 ** $value));
                    break;
                case Instruction::BXL:
                    $this->registerB = $this->registerB ^ $operand;
                    break;
                case Instruction::BST:
                    $value = $this->loadValue($operand);
                    $this->registerB = $value % 8;
                    break;
                case Instruction::JNZ:
                    if ($this->registerA !== 0) {
                        $this->pointer = $operand;
                    }
                    break;
                case Instruction::BXC:
                    $this->registerB = $this->registerB ^ $this->registerC;
                    break;
                case Instruction::OUT:
                    $value = $this->loadValue($operand);
                    $output[] = $value % 8;
                    break;
                case Instruction::BDV:
                    $value = $this->loadValue($operand);
                    $this->registerB = intval($this->registerA / (2 ** $value));
                    break;
                case Instruction::CDV:
                    $value = $this->loadValue($operand);
                    $this->registerC = intval($this->registerA / (2 ** $value));
                    break;
            }
        }

        return implode(',', $output);
    }

    private function loadValue(int $operand): int
    {
        return match ($operand) {
            4 => $this->registerA,
            5 => $this->registerB,
            6 => $this->registerC,
            7 => throw new \LogicException('Invalid operand'),
            default => $operand,
        };
    }
}
