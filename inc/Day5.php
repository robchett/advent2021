<?php

namespace Day5;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<list<Vent>>, int> */
class Solver extends \Task {

    /**
     * @param Vent[]
     * @return array<string, int>
     */
    public function get2dGrid(array $vents): array {
        $grid = [];
        foreach ($vents as $vent) {
            foreach(range($vent->start->x,$vent->end->x) as $x) {
                foreach(range($vent->start->y,$vent->end->y) as $y) {
                    $grid["$x,$y"] ??= 0;
                    $grid["$x,$y"]++;
                }
            }
        }
        return $grid;
    }

    public function part1(mixed $input): mixed {
        $grid = $this->get2dGrid(array_filter($input, fn(Vent $v) => $v->is2D));
        return count(array_filter($grid, fn(int $i): bool => $i >= 2));
    }

    public function part2(mixed $input): mixed {
        $grid = $this->get2dGrid(array_filter($input, fn(Vent $v) => $v->is2D));
        $filtered = array_filter($input, fn(Vent $v) => !$v->is2D);
        foreach ($filtered as $vent) {
            [$deltaX, $deltaY] = match([$vent->start->x < $vent->end->x, $vent->start->y < $vent->end->y]) {
                [true, true] => [1,1],
                [true, false] => [1,-1],
                [false, true] => [-1,1],
                [false, false] => [-1,-1],
            };
            [$x, $y] = [$vent->start->x, $vent->start->y];
            $grid["$x,$y"] ??= 0;
            $grid["$x,$y"]++;
            do {
                $x += $deltaX;
                $y += $deltaY;
                $grid["$x,$y"] ??= 0;
                $grid["$x,$y"]++;
            } while ($x != $vent->end->x);
        }

        return count(array_filter($grid, fn(int $i): bool => $i >= 2));
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day5_test1.txt', 5, \Stage::Stage1);
        $tests[] = new \Test($this, 'day5_test1.txt', 12, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \SimpleParser {
            function parseLine(string $line): Vent {
                preg_match('/(\d+),(\d+) -> (\d+),(\d+)/', $line, $matches);
                return new Vent(
                    new Coordinate((int)$matches[1], (int)$matches[2]),
                    new Coordinate((int)$matches[3], (int)$matches[4])
                );
            }
        };
    }
}

class Coordinate {
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {}
}

class Vent {

    public readonly bool $is2D;

    public function __construct(
        public readonly Coordinate $start,
        public readonly Coordinate $end,
    ) {
        $this->is2D = $this->start->x == $this->end->x || $this->start->y == $this->end->y;
    }
}