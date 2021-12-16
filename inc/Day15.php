<?php

namespace Day15;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Grid>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {        ;
        $start = [0,0];
        $end = [$input->width -1, $input->height -1];
        return $this->walk($input, $start, $end);
    }

    private function walk(Grid $grid): int {
        $optimalPaths = array_fill(0, $grid->width, array_fill(0, $grid->width, 0));
        for ($i = 1; $i < $grid->width; $i++) {
            $i0 = $i - 1;
            $optimalPaths[$i][0] = $optimalPaths[$i0][0] + $grid->grid[$i][0];
            $optimalPaths[0][$i] = $optimalPaths[0][$i0] + $grid->grid[0][$i];
            for ($x = 1; $x < $i; $x++) {
                $x0 = $x - 1;
                $optimalPaths[$i][$x] = min($optimalPaths[$i][$x0], $optimalPaths[$i0][$x]) + $grid->grid[$i][$x];
                $optimalPaths[$x][$i] = min($optimalPaths[$x0][$i], $optimalPaths[$x][$i0]) + $grid->grid[$x][$i];
            }
            $optimalPaths[$i][$i] = min($optimalPaths[$i0][$i], $optimalPaths[$i][$i0]) + $grid->grid[$i][$i];
        }
        do {
            $changedPaths = [];
            for ($x = 0; $x < $grid->width; $x++) {
                for ($y = 0; $y < $grid->width; $y++) {
                    foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as $dir) {
                        $xd = $x + $dir[0];
                        $yd = $y + $dir[1];
                        if ($xd > 0 && $xd < $grid->width && $yd > 0 && $yd < $grid->width &&
                            $optimalPaths[$yd][$xd] + $grid->grid[$y][$x] < $optimalPaths[$y][$x]
                        ) {
                            $old = $optimalPaths[$y][$x];
                            $new = $optimalPaths[$yd][$xd] + $grid->grid[$y][$x];
                            $changedPaths[$x][$y] = "${old} -> ${new}";
                            $optimalPaths[$y][$x] = $optimalPaths[$yd][$xd] + $grid->grid[$y][$x];
                        }
                    }
                }
            }
        } while($changedPaths);
        $i0 = $grid->width-1;

        //echo implode("\n", array_map(fn(array $s) => implode("\t", $s), $optimalPaths));

        return $optimalPaths[$i0][$i0];
    }

//    private function walk(Grid $grid, array $start, array $end): int {
//        $paths = [];
//        $optimalPaths = [];
//        $paths[""] = [0, 0, 0, ""];
//        $bestPath = false;
//        while ($path = array_pop($paths)) {
//            foreach (["L" => [1,0], 'D' => [0,1]] as $char => $direction) {
//                $newLoc = $path;
//                $newLoc[0] += $direction[0];
//                $newLoc[1] += $direction[1];
//                if ($newLoc[0] >= $grid->width) continue;
//                if ($newLoc[1] >= $grid->height) continue;
//                $newLoc[2] += $grid->grid[$newLoc[1]][$newLoc[0]];
//                if ($bestPath && $newLoc[2] >= $bestPath[2]) continue;
//                if (!isset($optimalPaths["{$newLoc[0]}, {$newLoc[1]}"])) {
//                    echo "Completed: " . count($optimalPaths) . " of " . ($grid->height * $grid->width) . " with " . (count($paths)) . " active paths\n";
//                    $optimalPaths["{$newLoc[0]}, {$newLoc[1]}"] = $newLoc[2];
//                } elseif ($optimalPaths["{$newLoc[0]}, {$newLoc[1]}"] <= $newLoc[2]) continue;
//                $newLoc[3] .= $char;
//                if ($newLoc[0] == $end[0] && $newLoc[1] == $end[1]) {
//                    echo "Found route: ${newLoc[2]}\n";
//                    $bestPath = $newLoc;
//                } else {
//                    $paths[] = $newLoc;
//                }
//            }
//        }
//        return $bestPath[2];
//    }

    public function part2(mixed $input): mixed {
        $newGrid = [];
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 5; $j++) {
                for ($x = 0; $x < $input->width; $x++) {
                    for ($y = 0; $y < $input->width; $y++) {
                        $newGrid[$x + ($input->width * $i)][$y + ($input->width * $j)] = ($input->grid[$x][$y] + $i + $j) % 9 + floor(($input->grid[$x][$y] + $i + $j) / 10);
                    }
                }
            }
        }
        $grid = new Grid($newGrid);
        return $this->walk($grid);
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day15_test1.txt', 40, \Stage::Stage1);
        $tests[] = new \Test($this, 'day15_test2.txt', 20, \Stage::Stage1);
        $tests[] = new \Test($this, 'day15_test1.txt', 315, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {
            function parse(array $lines): Grid {
                $grid = [];
                foreach ($lines as $line) {
                    $grid[] = array_map(fn(string $s) => (int)$s, str_split(trim($line)));
                }
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

class Path {
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly array $steps,
        public readonly Grid $grid
    ) { }
}