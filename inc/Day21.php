<?php

namespace Day21;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Game>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $p1 = clone $input->player1;
        $p2 = clone $input->player2;
        $dice = new DeterministicDice();
        while (true) {
            $p1->position += $dice->roll() + $dice->roll() + $dice->roll();
            $p1->score += (($p1->position - 1) % 10) + 1;
            if ($p1->score >= 1000) break;
            $p2->position += $dice->roll() + $dice->roll() + $dice->roll();
            $p2->score += (($p2->position - 1) % 10) + 1;
            if ($p2->score >= 1000) break;
        }
        return $dice->total * min($p1->score, $p2->score);
    }

    public function computeTurns(array $p1, array $p2): array {
        static $cache = [];
        if (isset($cache["{$p1[0]},{$p1[1]}, {$p2[0]}, {$p2[1]}"])) {
            return $cache["{$p1[0]},{$p1[1]}, {$p2[0]}, {$p2[1]}"];
        }
        $diceCombinations = [
            3 => 1,
            4 => 3,
            5 => 6,
            6 => 7,
            7 => 6,
            8 => 3,
            9 => 1,
        ];

        $p1Wins = $p2Wins = 0;

        foreach ($diceCombinations as $p1Score => $p1Combinations) {
            $newP1Position = $p1[0] + $p1Score;
            $newP1Score = $p1[1] + (($newP1Position - 1) % 10) + 1;
            if ($newP1Score >= 21) {
                $p1Wins += $p1Combinations;
                continue;
            }
            foreach ($diceCombinations as $p2Score => $p2Combinations) {
                $newP2Position = $p2[0] + $p2Score;
                $newP2Score = $p2[1] + (($newP2Position - 1) % 10) + 1;
                if ($newP2Score >= 21) {
                    $p2Wins += $p1Combinations * $p2Combinations;
                    continue;
                }
                [$subP1, $subP2] = $this->computeTurns([$newP1Position, $newP1Score], [$newP2Position, $newP2Score]);
                $p1Wins += $subP1 * $p1Combinations * $p2Combinations;
                $p2Wins += $subP2 * $p1Combinations * $p2Combinations;
            }
        }
        $cache["{$p1[0]},{$p1[1]}, {$p2[0]}, {$p2[1]}"] = [$p1Wins, $p2Wins];
        return [$p1Wins, $p2Wins];
    }

    public function part2(mixed $input): mixed {
        $res = $this->computeTurns([$input->player1->position, 0], [$input->player2->position, 0]);
        return max(...$res);
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day21_test1.txt', 739785, \Stage::Stage1);
        $tests[] = new \Test($this, 'day21_test1.txt', 444356092776315, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            /** @retrurn Game[] */
            function parse(array $lines): Game {
                preg_match('/Player 1 starting position: (\d)/', $lines[0], $p1);
                preg_match('/Player 2 starting position: (\d)/', $lines[1], $p2);
                return new Game(new Player((int)$p1[1]), new Player((int)$p2[1]));
            }
        };
    }
}

class Game {

    public function __construct(
        public readonly Player $player1,
        public readonly Player $player2,
    ) {
    }
}

class DeterministicDice {

    public int $roll = 0;
    public int $total = 0;

    public function roll(): int {
        $this->total++;
        return ($this->roll++ % 100) + 1;
    }
}

class DiracDice {

}

class Player {

    public function __construct(
        public int $position,
        public int $score = 0,
    ) {
    }
}