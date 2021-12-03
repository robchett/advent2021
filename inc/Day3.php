<?php

namespace Day3;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<list<Reading>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $gamma = [];
        $epsilon = [];
        for($i = 0; $i < strlen($input[0]->line) - 1; $i++) {
            $counts = [0, 0];
            foreach ($input as $reading) {
                $counts[$reading->line[$i]]++;
            }
            $gamma[] = $counts[0] > $counts[1] ? '0' : '1';
            $epsilon[] = $counts[0] > $counts[1] ? '1' : '0';
        }
        $gamma = implode('', $gamma);
        $epsilon = implode('', $epsilon);
        return bindec($gamma)*bindec($epsilon);
    }

    /** @param list<Reading> $input */
    private function filter(array $input, bool $gt): int {
        $remainingInput = $input;
        for($i = 0; $i < strlen($input[0]->line) - 1; $i++) {
            $counts = [0, 0];
            foreach ($remainingInput as $reading) {
                $counts[$reading->line[$i]]++;
            }
            $mode = match($gt) {
                true => $counts[0] <= $counts[1] ? '1' : '0',
                false => $counts[1] >= $counts[0] ? '0' : '1',
            };
            $remainingInput = array_values(array_filter($remainingInput, fn(Reading $r) => $r->line[$i] == $mode));
            if (count($remainingInput) == 1) {
                break;
            }
        }
        return bindec(reset($remainingInput)->line);
    }

    public function part2(mixed $input): mixed {
        return $this->filter($input, true) * $this->filter($input, false);
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day3_test1.txt', 198, \Stage::Stage1);
        $tests[] = new \Test($this, 'day3_test1.txt', 230, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class implements \LineParser {
            function parseLine(string $line): Reading {
                return new Reading($line);
            }
        };
    }
}

class Reading {
    public function __construct(
        public readonly string $line,
    ) {
    }
}