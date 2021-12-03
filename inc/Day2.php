<?php

namespace Day2;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<list<Instruction>, int> */
class Solver extends \Task {

    /** @param Instruction[] $input */
    public function compute(mixed $input, Position|PositionWithAim $position): int {
        foreach ($input as $instruction) {
            $method = match ($instruction->dir) {
                Direction::Forward => $position->moveForward(...),
                Direction::Up => $position->moveDown(...),
                Direction::Down => $position->moveUp(...),
            };
            $method($instruction->length);
        }
        return $position->getDistance();
    }

    public function part1(mixed $input): mixed {
        return $this->compute($input, new Position());
    }

    public function part2(mixed $input): mixed {
        return $this->compute($input, new PositionWithAim());
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day2_test1.txt', 150, \Stage::Stage1);
        $tests[] = new \Test($this, 'day2_test1.txt', 900, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class implements \LineParser {
            function parseLine(string $line): Instruction {
                [$dirStr, $lengthStr] = explode(' ', $line);
                return new Instruction(
                    match($dirStr) {
                        'forward' => Direction::Forward,
                        'up' => Direction::Up,
                        'down' => Direction::Down,
                    },
                    (int) $lengthStr
                );
            }
        };
    }
}

class Position {

    public function __construct(
        private int $x = 0,
        private int $y = 0,
    ) {

    }

    public function getDistance(): int {
        return abs($this->x * $this->y);
    }

    public function moveForward(int $x): void {
        $this->x += $x;
    }

    public function moveUp(int $y): void {
        $this->y -= $y;
    }

    public function moveDown(int $y): void {
        $this->y += $y;
    }
}

class PositionWithAim {

    public function __construct(
        private int $x = 0,
        private int $y = 0,
        private int $aim = 0,
    ) {

    }

    public function getDistance(): int {
        return abs($this->x * $this->y);
    }

    public function moveForward(int $x): void {
        $this->x += $x;
        $this->y += $this->aim * $x;
    }

    public function moveUp(int $y): void {
        $this->aim -= $y;
    }

    public function moveDown(int $y): void {
        $this->aim += $y;
    }
}

class Instruction {

    public function __construct(
        public readonly Direction $dir,
        public readonly int $length,
    ) {}

}

enum Direction {

    case Forward;
    case Up;
    case Down;

}