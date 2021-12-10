<?php

namespace Day10;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array<Bracket[]>>, int> */
class Solver extends \Task {

    /** @param Bracket[] $line */
    private function validateLine(array $line): ValidationResult {
        $stack = [];
        foreach($line as $c) {
            if (in_array($c, [Bracket::ANGLED_OPEN, Bracket::CURLY_OPEN, Bracket::ROUND_OPEN, Bracket::SQUARE_OPEN])) {
                $stack[] = $c;
                continue;
            }
            $last = array_pop($stack);
            $valid = match ($last) {
                Bracket::SQUARE_OPEN => $c == Bracket::SQUARE_CLOSE,
                Bracket::ROUND_OPEN => $c == Bracket::ROUND_CLOSE,
                Bracket::CURLY_OPEN => $c == Bracket::CURLY_CLOSE,
                Bracket::ANGLED_OPEN => $c == Bracket::ANGLED_CLOSE,
            };
            if (!$valid) {
                return new ValidationError($c);
            }
        }
        if ($stack) {
            return new ValidationIncomplete($stack);
        }
        return new ValidationSuccess();
    }

    public function part1(mixed $input): mixed {
        $lines = array_map($this->validateLine(...), $input);
        $lines = array_filter($lines, fn(ValidationResult $l) => $l instanceof ValidationError);
        return array_sum(array_map(fn(ValidationError $b) => match($b->bracket) {
            Bracket::ROUND_CLOSE => 3,
            Bracket::SQUARE_CLOSE => 57,
            Bracket::CURLY_CLOSE => 1197,
            Bracket::ANGLED_CLOSE => 25137,
        }, $lines));
    }


    public function part2(mixed $input): mixed {
        $lines = array_map($this->validateLine(...), $input);
        $lines = array_filter($lines, fn(ValidationResult $l) => $l instanceof ValidationIncomplete);
        $scores = array_map(fn(ValidationIncomplete $b) => $b->score(), $lines);
        sort($scores);
        return $scores[floor(count($scores) / 2)];
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day10_test1.txt', 26397, \Stage::Stage1);
        $tests[] = new \Test($this, 'day10_test1.txt', 288957, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            /** @return string[] */
            function parse(array $lines): array {
                return array_map(fn(string $s) => array_map(fn(string $b) => Bracket::from($b), str_split(trim($s))), $lines);
            }
        };
    }
}

class ValidationResult {

}

class ValidationSuccess extends ValidationResult {

}

class ValidationIncomplete extends ValidationResult {
    /** @param Bracket[] $remaining */
    public function __construct(public readonly array $remaining) {
    }

    public function score(): int {
        $score = 0;
        foreach (array_reverse($this->remaining) as $r) {
            $score *= 5;
            $score += match ($r) {
                Bracket::ROUND_OPEN => 1,
                Bracket::SQUARE_OPEN => 2,
                Bracket::CURLY_OPEN => 3,
                Bracket::ANGLED_OPEN => 4,
            };
        }
        return $score;
    }
}

class ValidationError extends ValidationResult {
    public function __construct(public readonly Bracket $bracket) {
    }
}

enum State {
    case CORRECT;
    case INCOMPLETE;
    case CORRUPT;
}

enum Bracket: string {
    case ROUND_OPEN = '(';
    case SQUARE_OPEN = '[';
    case CURLY_OPEN = '{';
    case ANGLED_OPEN = '<';
    case ROUND_CLOSE = ')';
    case SQUARE_CLOSE = ']';
    case CURLY_CLOSE = '}';
    case ANGLED_CLOSE = '>';
}