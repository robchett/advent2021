<?php

namespace Day11;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array<int>, int> */
class Solver extends \Task {

    /** @param array<int, array<int, int>> $grid */
    private function getValue(array $grid, int $x, int $y): int {
        return array_sum([
            $grid[$x+1][$y+1] ?? 0,
            $grid[$x+1][$y] ?? 0,
            $grid[$x+1][$y-1] ?? 0,
            $grid[$x][$y+1] ?? 0,
            $grid[$x][$y-1] ?? 0,
            $grid[$x-1][$y+1] ?? 0,
            $grid[$x-1][$y] ?? 0,
            $grid[$x-1][$y-1] ?? 0,
        ]);
    }

    public function part1(mixed $input): mixed {
        $grid = [];
        $grid[0][0] = 1;
        while(true) {
            $centerOffset = 1 + floor(count($grid) / 2);
            $bottom_right = [$centerOffset, 1 - $centerOffset];
            for ($i = $bottom_right[1]; $i <= $centerOffset; $i++) {
                $grid[$i][$bottom_right[0]] = $v = $this->getValue($grid, $i, $bottom_right[0]);
                if ($v > $input) return $v;
            }
            for ($i = $bottom_right[0] - 1; $i >= -$centerOffset; $i--) {
                $grid[$centerOffset][$i] = $v = $this->getValue($grid, $centerOffset, $i);
                if ($v > $input) return $v;
            }
            for ($i = $centerOffset - 1; $i >= -$centerOffset; $i--) {
                $grid[$i][-$centerOffset] = $v = $this->getValue($grid, $i, -$centerOffset);
                if ($v > $input) return $v;
            }
            for ($i = -$centerOffset + 1; $i <= $centerOffset; $i++) {
                $grid[-$centerOffset][$i] = $v = $this->getValue($grid, -$centerOffset, $i);
                if ($v > $input) return $v;
            }
        }
        return 0;
    }


    public function part2(mixed $input): mixed {
        return 0;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day11_test1.txt', 806, \Stage::Stage1);
        $tests[] = new \Test($this, 'day11_test1.txt', 0, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {
            function parse(array $lines): int {
                return (int) $lines[0];
            }
        };
    }
}