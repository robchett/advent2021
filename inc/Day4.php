<?php

namespace Day4;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Bingo>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $scores = array_map(fn(BingoBoard $b): ScoredBoard => $b->score($input->numbers), $input->boards);
        usort($scores, fn(ScoredBoard $a, ScoredBoard $b) => $a->turns <=> $b->turns);
        return $scores[0]->score;
    }

    public function part2(mixed $input): mixed {
        $scores = array_map(fn(BingoBoard $b): ScoredBoard => $b->score($input->numbers), $input->boards);
        usort($scores, fn(ScoredBoard $a, ScoredBoard $b) => $b->turns <=> $a->turns);
        return $scores[0]->score;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day4_test1.txt', 4512, \Stage::Stage1);
        $tests[] = new \Test($this, 'day4_test1.txt', 1924, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            /** @return int[] */
            private function parseInts(string $s, string $divider = ' '): array {
                return array_map(fn(string $i): int => (int)$i, explode($divider, trim(preg_replace('/\s+/', ' ', $s))));
            }

            function parse(array $lines): Bingo {
                $start = $this->parseInts(array_shift($lines), ',');
                $boards = [];
                while ($lines) {
                    array_shift($lines);
                    $l = array_map($this->parseInts(...), array_splice($lines, 0, 5));
                    for ($i = 0; $i < 5; $i++) {
                        $l[] = [$l[0][$i], $l[1][$i], $l[2][$i], $l[3][$i], $l[4][$i]];
                    }
                    $boards[] = new BingoBoard($l);
                }
                return new Bingo($start, $boards);
            }
        };
    }
}

class Bingo {

    /**
     * @param list<int> $numbers
     * @param list<BingoBoard> $boards
     */
    public function __construct(
        public readonly array $numbers,
        public readonly array $boards,
    ) {
    }
}

class BingoBoard {

    /**
     * @param list<int>[] $lines
     */
    public function __construct(
        public readonly array $lines,
    ) {
    }

    /** @param list<int> $numbers */
    public function score(array $numbers): ScoredBoard {
        for ($turn = 5; $turn < count($numbers); $turn++) {
            $turnNumbers = array_slice($numbers, 0, $turn);
            $winner = false;
            $unmarked = [];
            foreach ($this->lines as $line) {
                $lineUnmarked = array_diff($line, $turnNumbers);
                $winner |= count($lineUnmarked) == 0;
                    $unmarked = [...$unmarked, ...$lineUnmarked];
            }
            if ($winner) {
                $lastNumber = end($turnNumbers);
                $score = array_sum($unmarked) * $lastNumber / 2;
                return new ScoredBoard($this, $score, $turn);
            }
        }
        throw new \Exception('Board did not complete');
    }
}

class ScoredBoard {
    public function __construct(
        public readonly BingoBoard $board,
        public readonly int $score,
        public readonly int $turns,
    ) { }
}