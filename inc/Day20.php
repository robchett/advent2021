<?php

namespace Day20;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Enhancer>, int> */
class Solver extends \Task {

    public function enhance(Enhancer $input, int $count): mixed {
        $boundX = [0, count($input->grid[0])];
        $boundY = [0, count($input->grid)];
        $grid = $input->grid;

        $oob = false;
        for ($i = 0; $i < $count; $i++) {
            $newGrid = [];
            $grid[$boundY[0] - 1] = $grid[$boundY[0] - 2] = $grid[$boundY[1]] = $grid[$boundY[1] + 1] = [];

            for ($x = $boundX[0] - 1; $x <= $boundX[1]; $x++) {
                for ($y = $boundY[0] - 1; $y <= $boundY[1]; $y++) {
                    $flags = array_sum([
                        1 * ($grid[$y + 1][$x + 1] ?? $oob),
                        2 * ($grid[$y + 1][$x] ?? $oob),
                        4 * ($grid[$y + 1][$x - 1] ?? $oob),
                        8 * ($grid[$y][$x + 1] ?? $oob),
                        16 * ($grid[$y][$x] ?? $oob),
                        32 * ($grid[$y][$x - 1] ?? $oob),
                        64 * ($grid[$y - 1][$x + 1] ?? $oob),
                        128 * ($grid[$y - 1][$x] ?? $oob),
                        256 * ($grid[$y - 1][$x - 1] ?? $oob),
                    ]);
                    $newGrid[$y] ??= [];
                    $newGrid[$y][$x] = $input->binaryFlags[$flags];
                }
            }
            $oob = $input->binaryFlags[$oob ? 511 : 0];
            $boundX[0]--;
            $boundY[0]--;
            $boundX[1]++;
            $boundY[1]++;
            $grid = $newGrid;
        }
        return array_sum(array_map(array_sum(...), $grid));
    }

    public function part1(mixed $input): mixed {
        return $this->enhance($input, 2);
    }

    public function part2(mixed $input): mixed {
        return $this->enhance($input, 50);
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day20_test1.txt', 35, \Stage::Stage1);
        $tests[] = new \Test($this, 'day20_test1.txt', 3351, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            /** @retrurn Scanner[] */
            function parse(array $lines): Enhancer {
                $flags = array_map(fn(string $s) => $s == '#', str_split(trim(array_shift($lines))));
                array_shift($lines);
                $grid = [];
                foreach ($lines as $line) {
                    $grid[] = array_map(fn(string $s) => $s == '#', str_split(trim(array_shift($lines))));
                }
                return new Enhancer($flags, $grid);
            }
        };
    }
}

class Enhancer {

    public function __construct(
        public       readonly array $binaryFlags,
        public array $grid,
    ) {
    }
}