<?php

namespace Day18;

require_once 'Parser.php';
require_once 'SimpleParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Pair>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {
        $out = $this->reduce(clone array_shift($input));
        while($p = array_shift($input)) {
            $p = $this->reduce(clone $p);
            $out = new Pair($out, $p);
            $out = $this->reduce($out);
        }
        return $this->magnitude($out);
    }

    protected function reduce(Pair $p): Pair {
        while ($this->explodes($p, []) || $this->splits($p, [])) {}
        return $p;
    }

    protected function magnitude(Pair $p): int {
        $out = 0;
        $out += (is_int($p->l) ? $p->l : $this->magnitude($p->l)) * 3;
        $out += (is_int($p->r) ? $p->r : $this->magnitude($p->r)) * 2;
        return $out;
    }

    protected function explodes(int|Pair &$p, array $tree): bool {
        if (is_int($p->l) && is_int($p->r) && count($tree) > 3) {
            // Check for value on the left.
            foreach (array_reverse($tree) as $t) {
                if ($t[0] == 'l') {
                    // We took the left path so we need to go up a parent level.
                    continue;
                }
                if (is_int($t[1]->l)) {
                    $t[1]->l += $p->l;
                    break;
                }
                $parent = $t[1]->l;
                while(!is_int($parent->r)) {
                    $parent = $parent->r;
                }
                $parent->r += $p->l;
                break;
            }
            // To the same for the right value
            foreach (array_reverse($tree) as $t) {
                if ($t[0] == 'r') {
                    continue;
                }
                if (is_int($t[1]->r)) {
                    $t[1]->r += $p->r;
                    break;
                }
                $parent = $t[1]->r;
                while(!is_int($parent->l)) {
                    $parent = $parent->l;
                }
                $parent->l += $p->r;
                break;
            }
            $p = 0;
            return true;
        }
        if ($p->l instanceof Pair && $this->explodes($p->l, [...$tree, ['l', $p]])) {
            return true;
        }
        if ($p->r instanceof Pair && $this->explodes($p->r, [...$tree, ['r', $p]])) {
            return true;
        }
        return false;
    }

    protected function splits(int|Pair &$p, array $tree): bool {
        if (is_int($p)) {
            if ($p >= 10) {
                $p = new Pair((int) floor($p / 2), (int) ceil($p / 2));
                return true;
            }
            return false;
        }
        if ($this->splits($p->l, [...$tree, ['l', $p]])) {
            return true;
        }
        if ($this->splits($p->r, [...$tree, ['r', $p]])) {
            return true;
        }
        return false;
    }

    public function part2(mixed $input): mixed {
        $max = 0;
        $reductions = array_map($this->reduce(...), $input);

        for ($i = 0; $i < count($input); $i++) {
            for ($j = $i+1; $j < count($input); $j++) {
                $out["$i, $j"] = $this->magnitude($this->reduce(new Pair(clone $reductions[$i], clone $reductions[$j])));
                $out["$j, $i"] = $this->magnitude($this->reduce(new Pair(clone $reductions[$j], clone $reductions[$i])));
                $max = max($max, $out["$i, $j"], $out["$j, $i"]);
            }
        }
        asort($out);
        return $max;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day18_test1.txt', 4140, \Stage::Stage1);
        $tests[] = new \Test($this, 'day18_test1.txt', 3993, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \SimpleParser {
        return new class extends \SimpleParser {
            /** @retrurn Pair[] */
            function parseLine(string $line): Pair {
                return Pairer::pair($line);
            }
        };
    }
}

class Pairer {
    /** @return Pair */
    public static function pair(string $line): Pair {
        $pairs = [];
        $c = 0;
        while(str_contains($line, ',')) {
            $line = preg_replace_callback('/\[([\d£]+),([\d£]+)\]/', function($match) use (&$c, &$pairs) {
                $char = $c++;
                $pairs['£' . $char] = new Pair(
                    is_numeric($match[1]) ? (int) $match[1] : $pairs[$match[1]],
                    is_numeric($match[2]) ? (int) $match[2] : $pairs[$match[2]],
                );
                return '£' . $char;
            }, trim($line));
        }

        return $pairs[$line];
    }
}

class Pair {
    public function __construct(
        public Pair|int $l,
        public Pair|int $r,
    ) {}

    public function __clone() {
        $this->l = is_int($this->l) ? $this->l : clone $this->l;
        $this->r = is_int($this->r) ? $this->r : clone $this->r;
    }
}
