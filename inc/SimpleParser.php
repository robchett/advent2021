<?php

require_once 'LineParser.php';

/**
 * @template T
 * @extends LineParser<T>
 */
abstract class SimpleParser extends LineParser {

    /** @return T */
    abstract public function parseLine(string $line): mixed;

    /**
     * @param list<string> $lines
     * @return list<T>
     */
    public function parse(array $lines): array {
        $output = [];
        foreach ($lines as $line) {
            $output[] = $this->parseLine($line);
        }
        return $output;
    }

}