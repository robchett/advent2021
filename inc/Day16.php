<?php

namespace Day16;

require_once 'Parser.php';
require_once 'LineParser.php';
require_once 'Task.php';
require_once 'Test.php';
require_once 'Stage.php';

/** @extends \Task<Packet[]>, int> */
class Solver extends \Task {

    public function part1(mixed $input): mixed {        ;
        $versionSum = 0;
        foreach ($input as $packet) {
            $versionSum += $packet->versionSum();
        }
        return $versionSum;
    }

    public function part2(mixed $input): mixed {
        $versionSum = 0;
        foreach ($input as $packet) {
            $versionSum += $packet->value();
        }
        return $versionSum;
    }

    public function tests(): array {
        $tests = [];
        $tests[] = new \Test($this, 'day16_test1.txt', 16, \Stage::Stage1);
        $tests[] = new \Test($this, 'day16_test2.txt', 12, \Stage::Stage1);
        $tests[] = new \Test($this, 'day16_test3.txt', 23, \Stage::Stage1);
        $tests[] = new \Test($this, 'day16_test4.txt', 31, \Stage::Stage1);
        $tests[] = new \Test($this, 'day16_test5.txt', 3, \Stage::Stage2);
        $tests[] = new \Test($this, 'day16_test6.txt', 54, \Stage::Stage2);
        $tests[] = new \Test($this, 'day16_test7.txt', 7, \Stage::Stage2);
        $tests[] = new \Test($this, 'day16_test8.txt', 9, \Stage::Stage2);
        $tests[] = new \Test($this, 'day16_test9.txt', 1, \Stage::Stage2);
        $tests[] = new \Test($this, 'day16_test10.txt', 0, \Stage::Stage2);
        $tests[] = new \Test($this, 'day16_test11.txt', 0, \Stage::Stage2);
        $tests[] = new \Test($this, 'day16_test12.txt', 1, \Stage::Stage2);
        return $tests;
    }

    public function getParser(): \LineParser {
        return new class extends \LineParser {
            /** @retrurn Packet[] */
            function parse(array $lines): array {
                $bits = '';
                foreach (str_split(trim($lines[0])) as $hex) {
                    $bits .= str_pad(base_convert($hex, 16, 2), 4, '0', STR_PAD_LEFT);
                }
                $stream = new BitStream($bits);
                return $stream->parse();
            }
        };
    }
}

class BitStream {
    public function __construct(private string $bits) {

    }

    /** @return Packet[] */
    public function parse(int $max = 0): array {
        $packets = [];
        while($this->bits) {
            $version = $this->readDec(3);
            $type = $this->readDec(3);
            $packets[] = match ($type) {
                4 => new LiteralPacket($version, $type, $this->readLiteral()),
                default => new Packet($version, $type, $this)
            };
            if ($max && count($packets) == $max) {
                return $packets;
            }
        }
        return $packets;
    }

    public function read(int $length): string {
        [$out, $this->bits] = [substr($this->bits, 0, $length), substr($this->bits, $length)];
        return $out;
    }

    public function readDec(int $length): int {
        [$out, $this->bits] = [bindec(substr($this->bits, 0, $length)), substr($this->bits, $length)];
        return $out;
    }

    public function readLiteral(): int {
        $bits = '';
        while (true) {
            $out = $this->read(5);
            $bits .= substr($out, 1);
            if ($out[0] == 0) {
                break;
            }
        }

        return bindec($bits);
    }

}

class Packet {
    /** @var Packet[] */
    public array $subPackets = [];

    public function __construct(
        public readonly int $version,
        public readonly int $type,
        BitStream $stream,
    ) {
        $mode = (bool) $stream->read(1);
        if ($mode) {
            $this->subPackets = $stream->parse($stream->readDec(11));
        } else {
            $length = $stream->readDec(15);
            $subStream = new BitStream($stream->read($length));
            $this->subPackets = $subStream->parse();
        }
    }

    public function value(): int {
        return match ($this->type) {
            0 => array_sum(array_map(fn (Packet $p) => $p->value(), $this->subPackets)),
            1 => array_product(array_map(fn (Packet $p) => $p->value(), $this->subPackets)),
            2 => min(array_map(fn (Packet $p) => $p->value(), $this->subPackets)),
            3 => max(array_map(fn (Packet $p) => $p->value(), $this->subPackets)),
            5 => (int) ($this->subPackets[0]->value() > $this->subPackets[1]->value()),
            6 => (int) ($this->subPackets[0]->value() < $this->subPackets[1]->value()),
            7 => (int) ($this->subPackets[0]->value() == $this->subPackets[1]->value()),
        };
    }

    public function versionSum(): int {
        $sum = $this->version;
        foreach ($this->subPackets as $packet) {
            $sum += $packet->versionSum();
        }
        return $sum;
    }
}

class LiteralPacket extends Packet {

    public function __construct(
        public readonly int $version,
        public readonly int $type,
        public readonly int $value,
    ) {

    }

    public function value(): int {
        return $this->value;
    }
}