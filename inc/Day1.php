<?php

namespace Day1;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<list<int>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $p1 = -1;
        $prev = -INF;
        foreach ($input as $i) {
            if ($i > $prev) $p1++;
            $prev = $i;
        }
        return $p1;
    }

    public function part2(mixed $input): mixed {
        $p2 = -1;
        $window = [-INF, -INF, -INF];
        foreach ($input as $i) {
            $newWindow = [$window[1], $window[2], $i];
            if (array_sum($newWindow) > array_sum($window)) $p2++;
            $window = $newWindow;
        }
        return $p2;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day1_test1.txt', 7, \Stage::Stage1);
        $tests[] = new \Test($this, 'day1_test1.txt', 5, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class implements \LineParser {
            function parseLine(string $line): int {
                return (int)$line;
            }
        };
    }
};