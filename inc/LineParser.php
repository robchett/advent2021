<?php

/**
 * @template T
 */
interface LineParser {

    /**
     * @param string $line
     * @return T
     */
    function parseLine(string $line): mixed;
}