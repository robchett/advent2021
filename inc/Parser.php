<?php

class Parser {

    /**
     * @template T
     * @param int $day
     * @param LineParser<T> $parser
     * @return T[]
     */
    public static function parseLines(int $day, LineParser $parser): mixed {
        $input = file_get_contents(__DIR__ . "/../inputs/day$day.txt");
        $lines = explode("\n", $input);
        $output = [];
        foreach ($lines as $line) {
            $output[] = $parser->parseLine($line);
        }
        return $output;
    }
}