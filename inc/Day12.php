<?php

namespace Day12;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<array<string, Cave>, int> */
class Solver extends \Task {

    /** @param list<Cave> $input */
    private function traverse(int $revisits, array $input): int {
        $paths = [new Path($revisits, [$input['start']])];
        $completePaths = 0;
        while ($path = array_shift($paths)) {
            foreach ($path->last()->links as $next) {
                if ($next->type == CaveType::Start) {
                    continue;
                }
                if ($next->type == CaveType::End) {
                    $completePaths++;
                    continue;
                }
                if ($path->canVisit($next)) {
                    $paths[] = $path->visit($next);
                }
            }
        }
        return $completePaths;
    }

    public function part1(mixed $input): mixed {
        return $this->traverse(0, $input);
    }

    public function part2(mixed $input): mixed {
        return $this->traverse(1, $input);
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
        protected int $revisits,
        protected array $caves,
    ) {
    }

    public function last(): Cave {
        return end($this->caves);
    }

    public function visit(Cave $c): Path {
        $revisits = $this->revisits;
        if ($c->type == CaveType::Small && in_array($c, $this->caves)) {
            $revisits--;
        }
        return new Path($revisits, [...$this->caves, $c]);
    }

    public function canVisit(Cave $c): bool {
        return $c->type == CaveType::Big || !in_array($c, $this->caves) || $this->revisits;
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