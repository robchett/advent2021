<?php

namespace Day23;

require_once 'Parser.php';
require_once 'SimpleParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array{AmphipodType, AmphipodType}[]>, int> */
class Solver extends \Task {

    /**
     * #############
     * #..X.X.X.X..#
     * ###B#C#B#D###
     * #A#D#C#A#
     * #########
     */

    public function recurse(array $hallway, array $moves, int $size, int $depth) {
        static $seenPositions = [];
        $seenPositions[$size] ??= [];
        $wamtsMap = [
            2 => 'A',
            4 => 'B',
            6 => 'C',
            8 => 'D',
            'A' => 2,
            'B' => 4,
            'C' => 6,
            'D' => 8,
        ];

        $hallwayString = implode([
            $hallway[0] ?: '.',
            $hallway[1] ?: '.',
            '[', ...array_map(fn($c) => $c ?: '.' ,$hallway[2]), ']',
            $hallway[3] ?: '.',
            '[', ...array_map(fn($c) => $c ?: '.' ,$hallway[4]), ']',
            $hallway[5] ?: '.',
            '[', ...array_map(fn($c) => $c ?: '.' ,$hallway[6]), ']',
            $hallway[7] ?: '.',
            '[', ...array_map(fn($c) => $c ?: '.' ,$hallway[8]), ']',
            $hallway[9] ?: '.',
            $hallway[10] ?: '.',
        ]);
        if (isset($seenPositions[$size] [$hallwayString])) {
            return $seenPositions[$size] [$hallwayString];
        }
        $bestScore = PHP_INT_MAX;
        $seenPositions[$size] [$hallwayString] = PHP_INT_MAX;
        foreach ($moves as $move) {
            $newMoves = array_filter($moves, fn($m) => $m != $move);
            if (is_string($move)) {
                [$move, $extra] = explode(':', $move);
                $amphipod = $hallway[$move][$extra];

                foreach ($hallway as $cell => $value) {
                    if ($value !== false) continue;
                    if ($cell == $move) continue;
                    if (in_array($cell, [2,4,6,8]) && $wamtsMap[$cell] != $amphipod) {
                        continue;
                    }
                    foreach (range($move, $cell) as $between) {
                        if ($move == $between) continue;
                        if (is_string($hallway[$between])) continue 2;
                    }
                    $newHallway = $hallway;
                    $newnewmoves = $newMoves;
                    $newScore = (abs(((int)$move - $cell)) + (int) $extra + 1) * $this->cost($amphipod);
                    $newHallway[$cell] = $amphipod;
                    if (in_array($move, [2,4,6,8])) {
                        $newHallway[$move][$extra] = false;
                    } else {
                        $newHallway[$move] = false;
                    }
                    $newnewmoves[] = $cell;
                    if ($extra != ($size - 1)) {
                        $newnewmoves = [...$newnewmoves, "$move:" . ((int) $extra + 1)];
                    }
                    $bestScore = min($bestScore, $newScore + $this->recurse($newHallway, $newnewmoves, $size, $depth+1));
                    echo "";
                }
            } else {
                $amphipod = $hallway[$move];
                $target = $wamtsMap[$amphipod];
                if (array_filter($hallway[$target], fn($v) => $v != false && $v != $amphipod)) {
                    continue;
                }
                foreach (range($move, $target) as $between) {
                    if ($move == $between) continue;
                    if (is_string($hallway[$between])) continue 2;
                }
                $newHallway = $hallway;
                $extra = $size - count(array_filter($hallway[$target]));
                $newScore = (abs($target - $move) + $extra) * $this->cost($amphipod);

                $newHallway[$target][$extra - 1] = $amphipod;
                $newHallway[$move] = false;
                if (
                    $newHallway[2][0] == 'A' && $newHallway[2][$size-1] == 'A' &&
                    $newHallway[4][0] == 'B' && $newHallway[4][$size-1] == 'B' &&
                    $newHallway[6][0] == 'C' && $newHallway[6][$size-1] == 'C' &&
                    $newHallway[8][0] == 'D' && $newHallway[8][$size-1] == 'D'
                ) {
                    $bestScore = min($bestScore, $newScore);
                    echo "";
                } else {
                    $bestScore = min($bestScore, $newScore + $this->recurse($newHallway, $newMoves, $size, $depth +1));
                    echo "";
                }
            }
        }
        return $seenPositions[$size] [$hallwayString] = min($seenPositions[$size] [$hallwayString], $bestScore);
    }

    public function part1(mixed $input): mixed {
        return $this->recurse([false, false, $input[0], false, $input[1], false, $input[2], false, $input[3], false, false], ['2:0', '4:0', '6:0', '8:0'], count($input[0]), 0);
    }

    #D#C#B#A#
    #D#B#A#C#
    public function part2(mixed $input): mixed {
        return $this->part1([
            [$input[0][0], 'D', 'D', $input[0][1]],
            [$input[1][0], 'C', 'B', $input[1][1]],
            [$input[2][0], 'B', 'A', $input[2][1]],
            [$input[3][0], 'A', 'C', $input[3][1]],
        ]);
    }


    protected function cost(string $type): int {
        return match ($type) {
            'A' => 1,
            'B' => 10,
            'C' => 100,
            'D' => 1000,
        };
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day23_test1.txt', 12521, \Stage::Stage1);
        $tests[] = new \Test($this, 'day23_test1.txt', 44169, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            /** @return array{'A'|'B'|'C'|'D', 'A'|'B'|'C'|'D'}[] */
            function parse(array $lines): array {
                return [
                  [$lines[2][3],  $lines[3][3]],
                  [$lines[2][5],  $lines[3][5]],
                  [$lines[2][7],  $lines[3][7]],
                  [$lines[2][9],  $lines[3][9]],
                ];
            }
        };
    }
}