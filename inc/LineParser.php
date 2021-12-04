<?php

/**
 * @template T
 */
abstract class LineParser {

    /**
     * @param list<string> $lines
     * @return list<T>
     */
    abstract public function parse(array $lines): mixed;
}