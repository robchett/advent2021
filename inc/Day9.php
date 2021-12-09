<?php

namespace Day9;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Grid>, int> */
class Solver extends \Task {

    /** @return array{int, int}[] */
    private function findBasins(Grid $grid): array {
        $basins = [];
        for ($i = 1; $i < $grid->height - 1; $i++) {
            for ($j = 1; $j < $grid->width - 1; $j++) {
                $minNeighbor = min([
                    $grid->grid[$i - 1][$j],
                    $grid->grid[$i + 1][$j],
                    $grid->grid[$i][$j - 1],
                    $grid->grid[$i][$j + 1]
                ]);
                if ($grid->grid[$i][$j] < $minNeighbor) {
                    $basins[] = [$i,$j];
                }
            }
        }
        return $basins;
    }

    public function part1(mixed $input): mixed {        ;
        return array_sum(array_map(fn(array $basin) => $input->grid[$basin[0]][$basin[1]] +1, $this->findBasins($input)));
    }

    private function expandBasin(Grid $grid, int $x, int $y): int {
        $mappedLocations = [];
        $checkLocations = [[$x, $y]];
        while ($checkLocations) {
            $mappedLocations = [...$mappedLocations, ...$checkLocations];
            $newLocations = [];
            foreach ($checkLocations as $location) {
                foreach ([[1,0], [-1,0], [0,1], [0,-1]] as $direction) {
                    $newLoc = [$location[0] - $direction[0], $location[1] - $direction[1]];
                    if ($grid->grid[$newLoc[0]][$newLoc[1]] < 9 && !in_array($newLoc, $mappedLocations) && !in_array($newLoc, $newLocations)) {
                        $newLocations[] = $newLoc;
                    }
                }
            }
            $checkLocations = $newLocations;
        }
        return count($mappedLocations);
    }

    public function part2(mixed $input): mixed {
        $basinSizes = array_map(fn(array $basin) => $this->expandBasin($input, $basin[0], $basin[1]), $this->findBasins($input));
        sort($basinSizes);
        return array_product(array_slice(array_reverse($basinSizes), 0, 3));
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day9_test1.txt', 15, \Stage::Stage1);
        $tests[] = new \Test($this, 'day9_test1.txt', 1134, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            function parse(array $lines): Grid {
                $grid = [];
                foreach ($lines as $line) {
                    $grid[] = [10, ...array_map(fn(string $s) => (int)$s, str_split(trim($line))), 10];
                }
                array_unshift($grid, array_pad([], count($grid[0]), 10));
                array_push($grid, array_pad([], count($grid[0]), 10));
                return new Grid($grid);
            }
        };
    }
}

class Grid {

    public readonly int $width;
    public readonly int $height;

    /**
     * @param int[][] $grid
     */
    public function __construct(
        public readonly array $grid,
    ) {
        $this->width = count($this->grid[0]);
        $this->height = count($this->grid);
    }
}