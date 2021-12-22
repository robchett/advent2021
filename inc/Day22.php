<?php

namespace Day22;

require_once 'Parser.php';
require_once 'SimpleParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Command[]>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $onCells = [];
        foreach ($input as $command) {
            if ($command->cuboid->XMin > 50 || $command->cuboid->YMin > 50 || $command->cuboid->ZMin > 50 || $command->cuboid->XMax < -50 || $command->cuboid->YMax < -50 || $command->cuboid->ZMax < -50) {
                continue;
            }
            for ($x = max(-50, $command->cuboid->XMin); $x <= min(50, $command->cuboid->XMax); $x++) {
                for ($y = max(-50, $command->cuboid->YMin); $y <= min(50, $command->cuboid->YMax); $y++) {
                    for ($z = max(-50, $command->cuboid->ZMin); $z <= min(50, $command->cuboid->ZMax); $z++) {
                        if ($command->action == 'on') {
                            $onCells["{$x},{$y},{$z}"] = true;
                        } else {
                            unset($onCells["{$x},{$y},{$z}"]);
                        }
                    }
                }
            }
        }
        return count($onCells);
    }

    public function part2(mixed $input): mixed {
        $cuboids = [];
        foreach ($input as $i => $command) {
            $newCuboids = [];
            foreach ($cuboids as $cuboid) {
                $newCuboids = [...$newCuboids, ...$cuboid->overlaps($command->cuboid)];
            }
            if ($command->action == 'on') {
                $newCuboids[] = $command->cuboid;
            }
            $cuboids = $newCuboids;
        }
        return array_sum(array_map(fn(cuboid $c) => $c->size(), $cuboids));
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day22_test1.txt', 590784, \Stage::Stage1);
        $tests[] = new \Test($this, 'day22_test1.txt', 590784, \Stage::Stage2);
        $tests[] = new \Test($this, 'day22_test2.txt', 2758514936282235, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \SimpleParser {
        return new class extends \SimpleParser {

            function parseLine(string $line): Command {
                preg_match('/(on|off) x=([-\d]+)\.\.([-\d]+),y=([-\d]+)\.\.([-\d]+),z=([-\d]+)\.\.([-\d]+)/', $line, $matches);
                return new Command($matches[1], new cuboid((int)$matches[2], (int)$matches[3], (int)$matches[4], (int)$matches[5], (int)$matches[6], (int)$matches[7]), $matches[0]);
            }
        };
    }
}

class cuboid {

    public function __construct(
        public int $XMin,
        public int $XMax,
        public int $YMin,
        public int $YMax,
        public int $ZMin,
        public int $ZMax,
    ) {
    }

    public function size(): int {
        return ($this->XMax - $this->XMin + 1) * ($this->YMax - $this->YMin + 1) * ($this->ZMax - $this->ZMin + 1);
    }

    public static function arraySize(array $cubes): int {
        return array_sum(array_map(fn(cuboid $c) => $c->size(), $cubes));
    }

    protected function overlapX(cuboid $c): array {
        $cuboids = [];
        if (($x = new cuboid($this->XMin, $c->XMin - 1, $this->YMin, $this->YMax, $this->ZMin, $this->ZMax))->isValid()) {
            $cuboids = [...$cuboids, ...$x->overlapY($c)];
        }
        if (($x = new cuboid(max($this->XMin, $c->XMin), min($this->XMax, $c->XMax), $this->YMin, $this->YMax, $this->ZMin, $this->ZMax))->isValid()) {
            $cuboids = [...$cuboids, ...$x->overlapY($c, true)];
        }
        if (($x = new cuboid($c->XMax + 1, $this->XMax, $this->YMin, $this->YMax, $this->ZMin, $this->ZMax))->isValid()) {
            $cuboids = [...$cuboids, ...$x->overlapY($c)];
        }
        return $cuboids;
    }

    protected function overlapY(cuboid $c, $removeCenter = false): array {
        $cuboids = [];

        if (($y = new cuboid($this->XMin, $this->XMax, $this->YMin, $c->YMin - 1, $this->ZMin, $this->ZMax))->isValid()) {
            $cuboids = [...$cuboids, ...$y->overlapZ($c)];
        }
        if (($y = new cuboid($this->XMin, $this->XMax, max($this->YMin, $c->YMin), min($this->YMax, $c->YMax), $this->ZMin, $this->ZMax))->isValid()) {
            $cuboids = [...$cuboids, ...$y->overlapZ($c, $removeCenter)];
        }
        if (($y = new cuboid($this->XMin, $this->XMax, $c->YMax + 1, $this->YMax, $this->ZMin, $this->ZMax))->isValid()) {
            $cuboids = [...$cuboids, ...$y->overlapZ($c)];
        }
        return $cuboids;
    }

    protected function overlapZ(cuboid $c, $removeCenter = false): array {
        $cuboids = [];
        if (($z = new cuboid($this->XMin, $this->XMax, $this->YMin, $this->YMax, $this->ZMin, $c->ZMin - 1))->isValid()) {
            $cuboids[] = $z;
        }
        if (($z = new cuboid($this->XMin, $this->XMax, $this->YMin, $this->YMax, max($this->ZMin, $c->ZMin), min($this->ZMax, $c->ZMax)))->isValid()) {
            !$removeCenter && $cuboids[] = $z;
        }
        if (($z = new cuboid($this->XMin, $this->XMax, $this->YMin, $this->YMax, $c->ZMax + 1, $this->ZMax))->isValid()) {
            $cuboids[] = $z;
        }
        return $cuboids;
    }

    public function overlaps(cuboid $c): array {
        if ($this->XMin > $c->XMax || $this->YMin > $c->YMax || $this->ZMin > $c->ZMax || $this->XMax < $c->XMin || $this->YMax < $c->YMin || $this->ZMax < $c->ZMin) {
            return [$this];
        }
        return $this->overlapX($c);
    }

    public function isValid(): bool {
        return $this->XMin <= $this->XMax && $this->YMin <= $this->YMax && $this->ZMin <= $this->ZMax;
    }
}

class Command {

    public function __construct(
        public string $action,
        public cuboid $cuboid,
        public string $raw,
    ) {
    }
}