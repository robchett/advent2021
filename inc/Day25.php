<?php

namespace Day25;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array<string[]>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $steps = 0;
        $grid = $input;
        $maxX = count($input[0]);
        $maxY = count($input);
        do {
            $newGrid = [];
            $changes = false;
            for ($x = 0; $x < $maxX; $x++) {
                for ($y = 0; $y < $maxY; $y++) {
                    $newGrid[$y][$x] = $grid[$y][$x];
                }
            }
            for ($x = 0; $x < $maxX; $x++) {
                for ($y = 0; $y < $maxY; $y++) {
                    if ($grid[$y][$x] == '>' && $grid[$y][($x + 1) % $maxX] == '.') {
                        $changes = true;
                        $newGrid[$y][$x] = '.';
                        $newGrid[$y][($x + 1) % $maxX] = '>';
                    }
                }
            }
            $grid = $newGrid;
            for ($x = 0; $x < $maxX; $x++) {
                for ($y = 0; $y < $maxY; $y++) {
                    if ($grid[$y][$x] == 'v' && $grid[($y + 1) % $maxY][$x] == '.') {
                        $changes = true;
                        $newGrid[$y][$x] = '.';
                        $newGrid[($y + 1) % $maxY][$x] = 'v';
                    }
                }
            }
            $grid = $newGrid;
            $steps++;
        } while ($changes);
        return $steps;
    }

    public function part2(mixed $input): mixed {
        return 0;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day25_test1.txt', 58, \Stage::Stage1);
        $tests[] = new \Test($this, 'day25_test1.txt', 0, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            function parse(array $lines): array {
                return array_map(fn(string $line) => str_split(trim($line)), $lines);
            }
        };
    }
}