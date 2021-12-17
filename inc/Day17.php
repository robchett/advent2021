<?php

namespace Day17;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Target>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $bestHeight = 0;
        [$fastestX, $fastestY] = [$input->fastestX(), $input->fastestY()];
        for ($x = 1; $x <= $fastestX; $x++) {
            for ($y = 0; $y <= $fastestY; $y++) {
                if (($hit = $input->hit($x, $y)) !== false) {
                    $bestHeight = max($bestHeight, $hit);
                }
            }
        }
        return $bestHeight;
    }

    public function part2(mixed $input): mixed {
        $hits = [];
        [$fastestX, $fastestY] = [$input->fastestX(), $input->fastestY()];
        for ($x = 1; $x <= $fastestX; $x++) {
            for ($y = $input->minY; $y <= $fastestY; $y++) {
                if ($input->hit($x, $y) !== false) {
                    $hits[] = "$x, $y";
                }
            }
        }
        return count($hits);
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day17_test1.txt', 45, \Stage::Stage1);
        $tests[] = new \Test($this, 'day17_test1.txt', 112, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {
            /** @retrurn Target[] */
            function parse(array $lines): Target {
                preg_match('/x=(\d+)\.\.(\d+), y=([-\d]+)\.\.([-\d]+)/', $lines[0], $matches);
                return new Target((int) $matches[1],(int) $matches[3],(int) $matches[2],(int) $matches[4]);
            }
        };
    }
}

class Target {
    public function __construct(
        public readonly int $minX,
        public readonly int $minY,
        public readonly int $maxX,
        public readonly int $maxY,
    ) {

    }

    public function max(): array {
        return [$this->maxX, $this->maxY];
    }

    public function fastestX(): int {
        return $this->maxX;
    }

    public function fastestY(): int {
        return abs($this->maxY) + ($this->maxY - $this->minY);
    }

    public function hit(int $dX, int $dY): int|false {
        $x = $y = 0;
        $max = 0;
        while($x <= $this->maxX) {
            if ($dX == 0 && ($x < $this->minX || ($dY <= 0 && $y < $this->minY))) {
                return  false;
            }
            $x += $dX;
            $y += $dY;
            $max = max($max, $y);
            if ($x >= $this->minX && $x <= $this->maxX && $y >= $this->minY && $y <= $this->maxY) {
                return $max;
            }

            $dX = max(0, $dX - 1);
            $dY--;
        }
        return  false;
    }
}
