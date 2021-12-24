<?php

namespace Day24;

require_once 'Parser.php';
require_once 'SimpleParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {

        $consts = [];
        for ($i = 0; $i < 14; $i++) {
            $j = $i * 18;
            $consts[] = [(int)$input[$j + 4][2], (int) $input[$j + 5][2], (int) $input[$j + 15][2]];
        }

        $valids = [0 => ['']];
        for ($i = 13; $i >= 0; $i--) {
            $newValids = [];
            [$A, $B, $C] = $consts[$i];
            for ($w = 1; $w <= 9; $w++) {
                for ($z = 0; $z < 1000000; $z++) {
                    $v = (floor($z / $A) * ((25 * ((($z % 26) + $B) != $w)) + 1)) + (($w + $C) * ((($z % 26) + $B) != $w));
                    if (isset($valids[$v])) {
                        $newValids[$z] ??= [];
                        foreach ($valids[$v] as $o) {
                            $newValids[$z][] = "$w$o";
                        }
                    }
                }
            }
            $valids = $newValids;
        }
        $list = [];
        foreach ($valids as $arr) {
            $arr = [...$list, ...$arr];
        }
        return [max($arr), min($arr)];
    }


    public function part2(mixed $input): mixed {
        return 0;
    }

    public function print( mixed $input): string {
        $res = $this->part1($input);
        return "Part 1:" . $res[0] . "\nPart 2:" . $res[1] . "\n";
    }

    public function tests(): array {
        return [];
    }

    public function getParser(): \SimpleParser {
        return new class extends \SimpleParser {

            function parseLine(string $line): array {
                return explode(' ', trim($line));
            }
        };
    }
}