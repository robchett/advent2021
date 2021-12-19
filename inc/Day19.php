<?php

namespace Day19;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Pair>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $checked = [];
        while(count($input) > 1) {
            for ($i = 0; $i < count($input); $i++) {
                for ($j = $i + 1; $j < count($input); $j++) {
                    if (in_array("{$input[$i]->id} / {$input[$j]->id}", $checked)) {
                        echo "Not rechecking {$input[$i]->id} / {$input[$j]->id}\n";
                        continue;
                    }
                    if ($s = $input[$i]->overlay($input[$j])) {
                        array_push($input, $s);
                        unset($input[$i]);
                        unset($input[$j]);
                        $input = array_values($input);
                        echo "Created composite {$s->id}\n";
                        break 2;
                    } else {
                        $checked[] = "{$input[$i]->id} / {$input[$j]->id}";
                        echo "Could not merge {$input[$i]->id} / {$input[$j]->id}\n";
                    }
                }
            }
        }
        return [count($input[0]->beacons), $this->part2($input[0])];
    }

    public function part2(mixed $input): mixed {
        $max = 0;
        for($i = 0; $i < count($input->subScannerOffsets);$i++) {
            for ($j = $i + 1; $j < count($input->subScannerOffsets); $j++) {
                $max = max($max, array_sum([
                    abs($input->subScannerOffsets[$i]->x - $input->subScannerOffsets[$j]->x),
                    abs($input->subScannerOffsets[$i]->y - $input->subScannerOffsets[$j]->y),
                    abs($input->subScannerOffsets[$i]->z - $input->subScannerOffsets[$j]->z),
                ]));
            }
        }
        echo "Max was {$max}";
        return $max;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day19_test1.txt', [79, 3621], \Stage::Stage1);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {
            /** @retrurn Scanner[] */
            function parse(array $lines): array {
                $scannerCount = -1;
                $beacons = [];
                foreach (array_map(trim(...), $lines) as $line) {
                    if (str_contains($line, '---')) {
                        $scannerCount++;
                        continue;
                    }
                    if (strlen($line) == 0) {
                        $scanners[] = new Scanner($beacons, (string) $scannerCount, [new Coordinate(0,0,0)]);
                        $beacons = [];
                        continue;
                    }
                    [$x,$y,$z] = explode(',', $line);
                    $beacons[] = new Beacon(new Coordinate((int) $x, (int) $y, (int) $z));
                }
                if ($beacons) {
                    $scanners[] = new Scanner($beacons, (string) $scannerCount, [new Coordinate(0,0,0)]);
                }

                return $scanners;
            }
        };
    }

    public function print( mixed $input): string {
        $res = $this->part1($input);
        return "Part 1:" . $res[0] . "\nPart 2:" . $res[1] . "\n";
    }
}

class Coordinate {
    public function __construct(
        public int $x,
        public int $y,
        public int $z,
    ) { }

    public function get(string $t): int {
        return match ($t) {
            'x' => $this->x,
            '-x' => -$this->x,
            'y' => $this->y,
            '-y' => -$this->y,
            'z' => $this->z,
            '-z' => -$this->z,
        };
    }

    public function rotate(array $dir): self {
        return new Coordinate($this->get($dir[0]), $this->get($dir[1]), $this->get($dir[2]));
    }

    public function offset(array $pos): self {
        return new Coordinate(
            $this->x + $pos[0],
            $this->y + $pos[1],
            $this->z + $pos[2],
        );
    }

    public function __toString(): string {
        return "{$this->x},{$this->y},{$this->z}";
    }
}

class Beacon {
    public function __construct(
        public Coordinate $pos,
    ) { }

    public function offset(Beacon $b): Coordinate {
        return new Coordinate($this->pos->x - $b->pos->x, $this->pos->y - $b->pos->y, $this->pos->z - $b->pos->z);
    }
}

class Scanner {

    /** @var array<int, array<int, Coordinate>> */
    public array $relativePositions = [];

    /** @var Beacon[] $beacons */
    public function __construct(
        public array $beacons,
        public string $id,
        public array $subScannerOffsets = [],
    ) {
        for($i = 0; $i < count($this->beacons); $i++) {
            for($j = $i; $j < count($this->beacons); $j++) {
                $this->relativePositions[$i] ??= [];
                $this->relativePositions[$i][$j] = $this->beacons[$i]->offset($this->beacons[$j]);
                $this->relativePositions[$j] ??= [];
                $this->relativePositions[$j][$i] = $this->beacons[$j]->offset($this->beacons[$i]);
            }
        }
    }

    public function overlay(Scanner $b): Scanner|false {
        $dirs = [
            // Rotate around 'x'x4
            ['x', 'y', 'z'],
            ['x', 'z', '-y'],
            ['x', '-y', '-z'],
            ['x', '-z', 'y'],

            //Rotate 'y', then 'x'x3
            ['z', 'y', '-x'],
            ['z', '-x', '-y'],
            ['z', '-y', 'x'],
            ['z', 'x', 'y'],

            //Rotate 'y'x2, then 'x'x3
            ['-x', 'y', '-z'],
            ['-x', '-z', '-y'],
            ['-x', '-y', 'z'],
            ['-x', 'z', 'y'],

            //Rotate 'y'x3, then 'x'x3
            ['-z', 'y', 'x'],
            ['-z', '-x', 'y'],
            ['-z', '-y', '-x'],
            ['-z', '-x', 'y'],

            //Rotate 'z' ...
            ['y', '-x', 'z'],
            ['y', 'z', 'x'],
            ['y', 'x', '-z'],
            ['y', '-z', '-x'],

            //Rotate 'z'x3 ...
            ['-y', 'x', 'z'],
            ['-y', 'z', '-x'],
            ['-y', '-x', '-z'],
            ['-y', '-z', 'x'],
        ];

        foreach ($dirs as $dir) {
            $newRelativePositions = array_map(fn(array $positions) => array_map(fn (Coordinate $c) => $c->rotate($dir), $positions), $b->relativePositions);
            for ($i = 0; $i < count($this->relativePositions); $i++) {
                for ($j = 0; $j < count($newRelativePositions); $j++) {
                    $r1 = array_map(fn($c) => (string) $c, $this->relativePositions[$i]);
                    $r2 = array_map(fn($c) => (string) $c, $newRelativePositions[$j]);
                    if (count(array_intersect($r2, $r1)) < 12) {
                        continue;
                    }
                    $matchA = $this->beacons[array_key_first(array_filter($r1, fn($s) => $s == '0,0,0'))];
                    $origB = $b->beacons[array_key_first(array_filter($r2, fn($s) => $s == '0,0,0'))];
                    $matchB = new Coordinate($origB->pos->get($dir[0]), $origB->pos->get($dir[1]), $origB->pos->get($dir[2]));
                    $offset = [$matchA->pos->x - $matchB->x, $matchA->pos->y - $matchB->y, $matchA->pos->z - $matchB->z];
                    $newBeacons = array_map(fn(int $k) => new Beacon($b->beacons[$k]->pos->rotate($dir)->offset($offset)), array_keys(array_diff($r2, $r1)));
                    $newScanners = array_map(fn(Coordinate $pos) => $pos->rotate($dir)->offset($offset), $b->subScannerOffsets);
                    return new Scanner([...$this->beacons, ...$newBeacons], "{$this->id}+{$b->id}", [...$this->subScannerOffsets, ...$newScanners]);
                }
            }
        }
        return false;
    }
}
