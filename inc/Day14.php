<?php

namespace Day14;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array<string[], array<string,Instruction>>, int> */
class Solver extends \Task {

    private function run(string $compound, array $instructions, int $steps) {
        $pairs = [];
        $pairMap = [];
        foreach ($instructions as $instruction) {
            $pairMap[$instruction->input] = [substr($instruction->input, 0, 1) . $instruction->output, $instruction->output . substr($instruction->input, 1, 1)];
        }
        print_r($pairMap);
        for ($i = 0; $i < strlen($compound) -1; $i++) {
            $pairs[substr($compound, $i, 2)] ??= 0;
            $pairs[substr($compound, $i, 2)]++;
        }
        for ($i = 0; $i < $steps; $i++) {
            $newMap = [];
            foreach ($pairs as $pair => $count) {
                if (isset($pairMap[$pair])) {
                    $newMap[$pairMap[$pair][0]] ??= 0;
                    $newMap[$pairMap[$pair][0]] += $count;
                    $newMap[$pairMap[$pair][1]] ??= 0;
                    $newMap[$pairMap[$pair][1]] += $count;
                } else {
                    $newMap[$pair] ??= 0;
                    $newMap[$pair] += $count;
                }
            }
            $pairs = $newMap;
        }
        $letters = [];
        $letters[substr($compound, -1)] = 1;
        foreach ($pairs as $pair => $count) {
            $letters[$pair[0]] ??= 0;
            $letters[$pair[0]] += $count;
        }
        sort($letters);
        return end($letters) - reset($letters);
    }

    public function part1(mixed $input): mixed {
        [$compound, $instructions] = $input;
        return $this->run($compound, $instructions, 10);
    }

    public function part2(mixed $input): mixed {
        [$compound, $instructions] = $input;
        return $this->run($compound, $instructions, 40);
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day14_test1.txt', 1588, \Stage::Stage1);
        $tests[] = new \Test($this, 'day14_test1.txt', 2188189693529, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            /** @return array{string, Instruction[]} */
            function parse(array $lines): array {
                $compound = '';
                $instructions = [];
                foreach ($lines as $line) {
                    if (str_contains($line, '-') && preg_match('/(.*) -> (.*)/', trim($line), $matches) !== false) {
                        [$_, $input, $output] = $matches;
                        $instructions[$input] = new Instruction($input, $output);
                    } elseif (trim($line)) {
                        $compound = trim($line);
                    }
                }
                return [$compound, $instructions];
            }
        };
    }
}

class Instruction {

    public function __construct(
        public readonly string $input,
        public readonly string $output,
    ) {}

}