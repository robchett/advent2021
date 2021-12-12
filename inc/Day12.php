<?php

namespace Day12;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array<string, Cave>, int> */
class Solver extends \Task {

    /**
     * @return list<Path>
     */
    private function traverse(cave $start, cave $end): array {
        $paths = [new Path([$start])];
        $completePaths = [];
        while ($path = array_shift($paths)) {
            foreach ($path->last()->links as $next) {
                if ($next->type == CaveType::Start || ($end->type != CaveType::End && $next->type == CaveType::End)) {
                    continue;
                }
                if ($next == $end) {
                    $completePaths[] = $path;
                    continue;
                }
                if ($path->canVisit($next)) {
                    $paths[] = $path->visit($next);
                }
            }
        }
        return array_map(fn(Path $p) => new Path(array_filter($p->caves, fn(Cave $c) => $c->type == CaveType::Small)), $completePaths);
    }

    public function part1(mixed $input): mixed {
        return count($this->traverse($input['start'], $input['end']));
    }

    public function part2(mixed $input): mixed {
        $smallCaves = array_filter($input, fn(Cave $d) => $d->type == CaveType::Small);
        $baseLoops = array_map(fn(Cave $c) => $this->traverse($c, $c), $smallCaves);
        $paths = $this->traverse($input['start'], $input['end']);
        $out = count($paths);
        foreach ($paths as $path) {
            foreach ($path->caves as $cave) {
                $caveLoops = $baseLoops[$cave->name];
                foreach ($path->caves as $otherCave) {
                    if ($otherCave == $cave) {
                        continue;
                    }
                    $caveLoops = array_filter($caveLoops, fn(Path $p) => !in_array($otherCave, $p->caves));
                }
                $out += count($caveLoops);
            }
        }
        return $out;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day12_test1.txt', 10, \Stage::Stage1);
        $tests[] = new \Test($this, 'day12_test2.txt', 19, \Stage::Stage1);
        $tests[] = new \Test($this, 'day12_test3.txt', 226, \Stage::Stage1);
        $tests[] = new \Test($this, 'day12_test1.txt', 36, \Stage::Stage2);
        $tests[] = new \Test($this, 'day12_test2.txt', 103, \Stage::Stage2);
        $tests[] = new \Test($this, 'day12_test3.txt', 3509, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {

            /** @return array<string, Cave> */
            function parse(array $lines): array {
                $caves = [];
                foreach ($lines as $line) {
                    [$start, $end] = explode('-', trim($line));
                    $caves[$start] ??= new Cave($start, Cave::getType($start), []);
                    $caves[$end] ??= new Cave($end, Cave::getType($end), []);
                    $caves[$start]->addLink($caves[$end]);
                    $caves[$end]->addLink($caves[$start]);
                }
                return $caves;
            }
        };
    }
}

class Path {

    /** @param list<Cave> $caves */
    public function __construct(
        public array $caves,
    ) {
    }

    public function last(): Cave {
        return end($this->caves);
    }

    public function reduce(): string {
        return array_reduce($this->caves, fn(string $acc, Cave $c) => "$acc,{$c->name}" ,'') . ",end\n";
    }

    public function visit(Cave $c): Path {
        return new Path([...$this->caves, $c]);
    }

    public function canVisit(Cave $c): bool {
        return $c->type == CaveType::Big || !in_array($c, $this->caves);
    }
}

class Cave {

    /**
     * @param CaveType $type
     * @param Cave[] $links
     */
    public function __construct(
        public       readonly string $name,
        public       readonly CaveType $type,
        public array $links
    ) {
    }

    public function addLink(Cave $c) {
        $this->links[$c->name] = $c;
    }

    public static function getType(string $s): CaveType {
        if ($s == 'start') return CaveType::Start;
        if ($s == 'end') return CaveType::End;
        if ($s[0] === strtolower($s[0])) return CaveType::Small;
        return CaveType::Big;
    }
}

enum CaveType {

    case Start;
    case End;
    case Small;
    case Big;
}