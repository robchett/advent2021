<?php

/**
 * @template TIn
 * @template TOut
 */
abstract class Task {

    /**
     * @param TIn $input
     * @return TOut
     */
    abstract public function part1(mixed $input): mixed;

    /**
     * @param TIn $input
     * @return TOut
     */
    abstract public function part2(mixed $input): mixed;

    /** @return Test[] */
    abstract public function tests(): array;

    abstract public function getParser(): LineParser;

}