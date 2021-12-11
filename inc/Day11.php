<?php

namespace Day11;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array<int, array<int, int>>, int> */
class Solver extends \Task {

    /**
     * @param array<int, array<int, int>> $grid
     * @return array{array<int, array<int, int>>, int}
     */
    private function flash(array $grid, int $x, int $y, int $flashes): array {
        $flashes++;
        foreach ([
                     [$x - 1, $y + 1],
                     [$x, $y + 1],
                     [$x + 1, $y + 1],
                     [$x - 1, $y],
                     [$x + 1, $y],
                     [$x + 1, $y - 1],
                     [$x, $y - 1],
                     [$x - 1, $y - 1],
                 ] as $offset) {
            if ($offset[0] >= 0 && $offset[0] <= 9 && $offset[1] >= 0 && $offset[1] <= 9) {
                $grid[$offset[0]][$offset[1]]++;
                if ($grid[$offset[0]][$offset[1]] == 10) {
                    [$grid, $flashes] = $this->flash($grid, $offset[0], $offset[1], $flashes);
                }
            }
        }
        return [$grid, $flashes];
    }

    /**
     * @param array<int, array<int, int>> $grid
     * @return array{array<int, array<int, int>>, int}
     */
    private function iterate(array $grid): array {
        $flashes = 0;
        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                $grid[$x][$y]++;
                if ($grid[$x][$y] == 10) {
                    [$grid, $flashes] = $this->flash($grid, $x, $y, $flashes);
                }
            }
        }
        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                if ($grid[$x][$y] >= 10) {
                    $grid[$x][$y] = 0;
                }
            }
        }
        return [$grid, $flashes];
    }

    public function part1(mixed $grid): mixed {
        $flashes = 0;
        for ($i = 0; $i < 100; $i++) {
            [$grid, $iterFlashes] = $this->iterate($grid);
            $flashes+=$iterFlashes;
        }
        return $flashes;
    }

    public function part2(mixed $grid): mixed {
        $step = 0;
        while (++$step) {
            [$grid, $flashes] = $this->iterate($grid);
            if ($flashes == 100) {
                return $step;
            }
        }
        return 0;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day11_test1.txt', 1656, \Stage::Stage1);
        $tests[] = new \Test($this, 'day11_test1.txt', 195, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {
            function parse(array $lines): array {
                return array_map(fn(string $s) => array_map(fn(string $c) => (int)$c, str_split(trim($s))), $lines);
            }
        };
    }
}