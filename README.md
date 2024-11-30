# 🎄Advent of Code

[Advent of Code](https://adventofcode.com/) is an annual event featuring daily programming puzzles designed for various
skill levels. Created by Eric Wastl, the event runs every December, encouraging participants to solve challenges in
their
preferred programming language. It serves as a platform for learning, interview preparation, company training,
coursework,
or friendly competition.

# 💻 About

This repository contains PHP template as well as my own solutions to the Advent of Code challenges. The solutions
will be published daily the day after the challenge is released. The main purpose of this repository is to
keep track of my progress, inspire others, compare solutions and showcase different approaches to solving the same
problem.

# 🧱 Template

The `template` branch contains only the template (command wrapper) for the PHP solution classes.
For each day of the challenge:

- create a new class implementing the `AdventOfCode\Solution\SolutionInterface` interface inside the
  `src/Solution/Solver/` directory,
- create an input file in the `input/` directory,

Both the class and the input file should be named according to the day of the challenge - `DayXX` where `XX` is the 2
digit day number.

# ❓ How to run the code?

To run the code, you need to have PHP 8.4 installed on your machine. You can run the code by executing the following
command:

```shell
php aod solve <day>
```

Where `<day>` is the day of the challenge you want to run. For example, to run the solution for day 3, you would
execute:

```shell
php aod solve 3
```

## Benchmarking

To benchmark the code, you need to pass `--benchmark` option to the command:

```shell
php aod solve --benchmark 3
```

Benchmarking is done by executing the solution 1000 times and calculating the average execution time and memory usage.
