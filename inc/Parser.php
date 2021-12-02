<?php

class Parser {

    /**
     * @template T
     * @param LineParser<T> $parser
     * @return T[]
     */
    public static function parseLines(string $inputFile, LineParser $parser): mixed {
        $input = file_get_contents(__DIR__ . "/../inputs/$inputFile");
        $lines = explode("\n", $input);
        $output = [];
        foreach ($lines as $line) {
            $output[] = $parser->parseLine($line);
        }
        return $output;
    }
}