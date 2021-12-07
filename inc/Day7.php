<?php

namespace Day7;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<list<int>>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $sum = array_sum($input);
        $count = count($input);
        $avg = floor($sum / $count);
        $pos = $avg;

        $min = array_reduce($input, fn(int $acc, int $v) => $acc + abs($v - $pos), 0);
        $i = 1;
        while(true) {
            $minD = array_reduce($input, fn(int $acc, int $v) => $acc + abs($v - $pos - $i), 0);
            $minU = array_reduce($input, fn(int $acc, int $v) => $acc + abs($v - $pos + $i), 0);
            if ($min <= $minD && $min <= $minU) {
                return $min;
            }
            $min = min($minU, $minD);
            $i++;
        }
    }

    private function sumOfNaturalNumbers(int $i): int {
        return ($i * ($i + 1) / 2);
    }

    public function part2(mixed $input): mixed {
        $sum = array_sum($input);
        $count = count($input);
        $avg = floor($sum / $count);
        $pos = $avg;

        $min = array_reduce($input, fn(int $acc, int $v) => $acc + $this->sumOfNaturalNumbers(abs($v - $pos)), 0);
        $i = 1;
        while(true) {
            $minD = array_reduce($input, fn(int $acc, int $v) => $acc + $this->sumOfNaturalNumbers(abs($v - $pos - $i)), 0);
            $minU = array_reduce($input, fn(int $acc, int $v) => $acc + $this->sumOfNaturalNumbers(abs($v - $pos + $i)), 0);
            if ($min <= $minD && $min <= $minU) {
                return $min;
            }
            $min = min($minU, $minD);
            $i++;
        }
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day7_test1.txt', 37, \Stage::Stage1);
        $tests[] = new \Test($this, 'day7_test1.txt', 168, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {
            /** @return Bucket[] */
            function parse(array $lines): array {
                $values = explode(',', $lines[0]);
                return array_map(fn(string $s) => (int) $s, $values);
            }
        };
    }
}

class Bucket {

    public function __construct(
        public readonly int $offset,
        public readonly int $fish,
    ) {
    }
}