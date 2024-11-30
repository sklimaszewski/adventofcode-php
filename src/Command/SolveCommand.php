<?php

declare(strict_types=1);

namespace AdventOfCode\Command;

use AdventOfCode\Exception\NotImplementedException;
use AdventOfCode\Solution\SolverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class SolveCommand extends Command
{
    private const string OPTION_BENCHMARK = 'benchmark';
    private const string ARGUMENT_DAY = 'day';

    private const int BENCHMARK_ITERATIONS = 1000;

    protected function configure(): void
    {
        $this
            ->setName('solve')
            ->setDescription('Solves the Advent of Code puzzle for a given day')
            ->addOption(self::OPTION_BENCHMARK, 'b', InputOption::VALUE_NONE, sprintf('Benchmark the solver by running it %d times', self::BENCHMARK_ITERATIONS))
            ->addArgument(self::ARGUMENT_DAY, InputOption::VALUE_REQUIRED, 'The day of the puzzle')
            ->setHelp(
                <<<'EOT'
                             |
                            -+- 
                             A
                            /=\              _      _             _   
                          i/ O \i           /_\  __| |_ _____ _ _| |_ 
                          /=====\          / _ \/ _` \ V / -_) ' \  _|
                          /  i  \         /_/ \_\__,_|\_/\___|_||_\__|
                        i/ O * O \i        ___ / _|                   
                        /=========\       / _ \  _|                   
                        /  *   *  \       \___/_|       _             
                      i/ O   i   O \i      / __|___  __| |___         
                      /=============\     | (__/ _ \/ _` / -_)        
                      /  O   i   O  \      \___\___/\__,_\___|        
                    i/ *   O   O   * \i
                    /=================\
                           |___|
                    EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $day = $input->getArgument(self::ARGUMENT_DAY);
        if (!is_numeric($day)) {
            $output->writeln('<error>Day must be a number.</error>');
            return Command::FAILURE;
        }

        $className = sprintf('AdventOfCode\Solution\Solver\Day%02d', (int) $day);
        if (!class_exists($className)) {
            $output->writeln(sprintf('<error>Class %s does not exist.</error>', $className));
            return Command::FAILURE;
        }

        $inputFile = __DIR__ . sprintf('/../../input/Day%02d.txt', (int) $day);
        if (!file_exists($inputFile)) {
            $output->writeln(sprintf('<error>Input file %s does not exist.</error>', $inputFile));
            return Command::FAILURE;
        }

        $solution = new $className($output);
        if (!$solution instanceof SolverInterface) {
            $output->writeln(sprintf('<error>Class %s does not implement SolutionInterface.</error>', $className));
            return Command::FAILURE;
        }

        $stopwatch = new Stopwatch();

        if ($input->getOption(self::OPTION_BENCHMARK)) {
            $output->writeln('<question>*****************************************************</question>');
            $output->writeln(sprintf('<question>*** Advent of Code - benchmarking day %02d solution ***</question>', $day));
            $output->writeln('<question>*****************************************************</question>');
            $output->writeln('');

            $totalTime = 0;
            $totalMemory = 0;

            $progressBar = new ProgressBar($output, self::BENCHMARK_ITERATIONS);
            $progressBar->setFormat('very_verbose');
            $progressBar->start();

            $stopwatch->start('solver');

            for ($i = 0; $i < self::BENCHMARK_ITERATIONS; ++$i) {
                $stopwatch->start(sprintf('solver-%d', $i));

                $solution->solveFirstPart($inputFile);
                try {
                    $solution->solveSecondPart($inputFile);
                } catch (NotImplementedException) {
                    // Ignore exception
                }

                $event = $stopwatch->stop(sprintf('solver-%d', $i));

                $totalTime += $event->getDuration();
                $totalMemory += $event->getMemory();

                $progressBar->advance();
            }

            $progressBar->finish();
            $output->writeln('');
            $output->writeln('');

            $output->writeln(sprintf('<comment>Average time: %.2f ms</comment>', $totalTime / self::BENCHMARK_ITERATIONS));
            $output->writeln(sprintf('<comment>Average memory: %s</comment>', Helper::formatMemory($totalMemory / self::BENCHMARK_ITERATIONS)));
            $output->writeln('');

            $event = $stopwatch->stop('solver');
            $output->writeln(sprintf('<comment>Total time: %.2f ms</comment>', $event->getDuration()));
            $output->writeln(sprintf('<comment>Memory: %s</comment>', Helper::formatMemory($event->getMemory())));
        } else {
            $output->writeln('<question>***************************************</question>');
            $output->writeln(sprintf('<question>*** Advent of Code - solving day %02d ***</question>', $day));
            $output->writeln('<question>***************************************</question>');
            $output->writeln('');

            $stopwatch->start('solver-first-part');
            $result = $solution->solveFirstPart($inputFile);
            $event = $stopwatch->stop('solver-first-part');

            $output->writeln('<info>First part result:</info>');
            $output->writeln($result);
            $output->writeln('');
            $output->writeln(sprintf('<comment>Time: %.2f ms</comment>', $event->getDuration()));
            $output->writeln(sprintf('<comment>Memory: %s</comment>', Helper::formatMemory($event->getMemory())));

            try {
                $output->writeln('');

                $stopwatch->start('solver-second-part');
                $result = $solution->solveSecondPart($inputFile);
                $event = $stopwatch->stop('solver-second-part');

                $output->writeln('<info>Second part result:</info>');
                $output->writeln($result);
                $output->writeln('');
                $output->writeln(sprintf('<comment>Time: %.2f ms</comment>', $event->getDuration()));
                $output->writeln(sprintf('<comment>Memory: %s</comment>', Helper::formatMemory($event->getMemory())));
            } catch (NotImplementedException) {
                // Ignore exception
            }
        }

        return Command::SUCCESS;
    }
}
