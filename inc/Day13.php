<?php

namespace Day13;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array<string, Cave>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $folder = new Folder($input[0], [$input[1][0]]);
        $res = $folder->fold(false);
        $counter = new Counter($res);
        return $counter->count();
    }

    public function part2(mixed $input): mixed {
        $folder = new Folder($input[0], $input[1]);
        $res = $folder->fold(true);
        return 0;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day13_test1.txt', 17, \Stage::Stage1);
        $tests[] = new \Test($this, 'day13_test1.txt', 0, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            /** @return array{array<int, array<int, string>>, Instruction[]} */
            function parse(array $lines): array {
                $grid = [];
                $instructions = [];

                foreach ($lines as $line) {
                    if (str_contains($line, ',')) {
                        [$x, $y] = explode(',', trim($line));
                        $grid[$x] ??= [];
                        $grid[$y][$x] = '#';
                    } elseif (str_contains($line, '=')) {
                        preg_match('/ (.)=(\d+)/', $line, $matches);
                        [$_, $dir, $pos] = $matches;
                        $instructions[] = new Instruction(Direction::from($dir), (int)$pos);
                    }
                }
                return [$grid, $instructions];
            }
        };
    }
}

class Counter {

    public function __construct(protected readonly array $grid) {
    }

    public function count(): int {
        return array_reduce($this->grid, fn(int $acc, array $line) => $acc + count(array_filter($line, fn(string $c) => $c == '#')), 0);
    }
}

class Folder {

    /**
     * @param array<int, array<int, string>> $grid
     * @param Instruction[] $instructions
     */
    public function __construct(protected array $grid, protected array $instructions) {
    }

    private function merge(string $s1, string $s2): string {
        return ($s1 == '#' || $s2 == '#') ? '#' :  '.';
    }

    /**
     * @param array<int, array<int, string>> $grid
     * @return array<int, array<int, string>>
     */
    private function foldX(array $grid, int $pos): array {
        $newGrid = [];
        foreach ($grid as $y => $line) {
            for ($x = 0; $x < $pos; $x++) {
                $newGrid[$y][$x] = $this->merge($grid[$y][$x] ?? '.', $grid[$y][$pos * 2 - $x] ?? '.');
            }
        }
        return $newGrid;
    }

    /**
     * @param array<int, array<int, string>> $grid
     * @return array<int, array<int, string>>
     */
    private function foldY(array $grid, int $pos): array {
        $newGrid = [];
        $max = max(array_map(fn(array $line) => $line ? max(array_keys($line)) : 0, $grid));
        for ($x = 0; $x <= $max; $x++) {
            for ($y = 0; $y < $pos; $y++) {
                $newGrid[$y][$x] = $this->merge($grid[$y][$x] ?? '.', $grid[$pos * 2 - $y][$x] ?? '.');
            }
        }
        return $newGrid;
    }

    private function print(array $grid): void {
        $out = '';
        $max = max(array_map(fn(array $line) => $line ? max(array_keys($line)) : 0, $grid));
        for ($y = 0; $y <= count($grid) + 1; $y++) {
            for ($x = 0; $x <= $max; $x++) {
                $out .= $grid[$y][$x] ?? '.';
            }
            $out .= "\n";
        }
        echo "$out\n\n";
    }

    /** @return array<int, array<int, string>> */
    public function fold(bool $print = false): array {
        $grid = $this->grid;
        foreach ($this->instructions as $instruction) {
            $grid = match ($instruction->dir) {
                Direction::X => $this->foldX($grid, $instruction->pos),
                Direction::Y => $this->foldY($grid, $instruction->pos),
            };
        }
        if ($print) $this->print($grid);
        return $grid;
    }
}

enum Direction: string {

    case X = 'x';
    case Y = 'y';
}

class Instruction {

    public function __construct(
        public readonly Direction $dir,
        public readonly int $pos,
    ) {
    }
}