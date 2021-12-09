<?php

namespace Day8;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<list<Line>>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $cnt = 0;
        foreach ($input as $line) {
            foreach ($line->output as $o) {
                $cnt += in_array(strlen($o), [2, 4, 3, 7]);
            }
        }
        return $cnt;
    }

    private function contains(string $s, string $t): bool {
        return $this->diff($s, $t) == 0;
    }

    private function diff(string $s, string $t): int {
        return count(array_diff(str_split($t), str_split($s)));
    }

    private function equals(string $s, string $t): bool {
        return strlen($s) == strlen($t) && $this->diff($s, $t) == 0;
    }

    public function part2(mixed $input): mixed {
        $cnt = 0;
        foreach ($input as $line) {
            $d1 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 2))[0];
            $d4 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 4))[0];
            $d7 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 3))[0];
            $d8 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 7))[0];
            $d9 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 6 && $this->contains($s, $d4)))[0];
            $d0 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 6 && $this->contains($s, $d7) && !$this->contains($s, $d9)))[0];
            $d6 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 6 && !$this->contains($s, $d9) && !$this->contains($s, $d0)))[0];
            $d3 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 5 && $this->contains($s, $d7)))[0];
            $d5 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 5 && $this->diff($s, $d6) == 1))[0];
            $d2 = array_values(array_filter($line->input, fn(string $s) => strlen($s) == 5 && !$this->contains($s, $d5)  && !$this->contains($s, $d3)))[0];

            $str = [];
            foreach ($line->output as $o) {
                $char = match (true) {
                    $this->equals($o, $d0) => '0',
                    $this->equals($o, $d1) => '1',
                    $this->equals($o, $d2) => '2',
                    $this->equals($o, $d3) => '3',
                    $this->equals($o, $d4) => '4',
                    $this->equals($o, $d5) => '5',
                    $this->equals($o, $d6) => '6',
                    $this->equals($o, $d7) => '7',
                    $this->equals($o, $d8) => '8',
                    $this->equals($o, $d9) => '9',
                };
                $str[] = $char;
            }
            $str = implode('', $str);
            $cnt += (int) $str;
        }
        return $cnt;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day8_test1.txt', 26, \Stage::Stage1);
        $tests[] = new \Test($this, 'day8_test1.txt', 61229, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \SimpleParser {
        return new class extends \SimpleParser {

            function parseLine(string $line): Line {
                [$input, $output] = explode(' | ', trim($line));
                return new Line(explode(' ', $input), explode(' ', $output));
            }
        };
    }
}

class Line {

    /**
     * @param string[] $input
     * @param string[] $output
     */
    public function __construct(
        public readonly array $input,
        public readonly array $output,
    ) {
    }
}