<?php

namespace Day6;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<list<Bucket>>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $delayed = [];
        for($i = 0; $i < 80; $i++) {
            $existing = $i % 7;
            $spawn = ($i + 2) % 7;
            $delayed[$spawn] = $input[$existing]->fish;
            $input[$existing] = new Bucket($existing, $input[$existing]->fish + ($delayed[$existing] ?? 0));
            $delayed[$existing] = 0;
        }
        return array_reduce($input, fn(int $acc, Bucket $b) => $acc + $b->fish, 0) + array_sum($delayed);
    }

    public function part2(mixed $input): mixed {
        $delayed = [];
        for($i = 0; $i < 256; $i++) {
            $existing = $i % 7;
            $spawn = ($i + 2) % 7;
            $delayed[$spawn] = $input[$existing]->fish;
            $input[$existing] = new Bucket($existing, $input[$existing]->fish + ($delayed[$existing] ?? 0));
            $delayed[$existing] = 0;
        }
        return array_reduce($input, fn(int $acc, Bucket $b) => $acc + $b->fish, 0) + array_sum($delayed);    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day6_test1.txt', 5934, \Stage::Stage1);
        $tests[] = new \Test($this, 'day6_test1.txt', 26984457539, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {
            /** @return Bucket[] */
            function parse(array $lines): array {
                $values = explode(',', $lines[0]);
                $groups = [];
                foreach ($values as $v) {
                    $groups[$v] ??= 0;
                    $groups[$v]++;
                }
                $output = [];
                for ($i = 0; $i < 7; $i++) {
                    $output[$i] = new Bucket($i, $groups[$i] ?? 0);
                }
                return $output;
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