<?php

require_once 'Stage.php';

/**
 *  @template Tin
 *  @template Tout
 */
class Test {

    /**
     * @param Task<Tin, Tout> $task
     * @param Tout $output
     * @param Stage $stage
     */
    public function __construct(
        readonly public Task $task,
        readonly public string $inputFile,
        readonly public mixed $output,
        readonly public Stage $stage,
    ) {

    }

    public function run(): bool {
        $method = match ($this->stage) {
            Stage::Stage1 => $this->task->part1(...),
            Stage::Stage2 => $this->task->part2(...),
        };
        $input = Parser::parseLines($this->inputFile, $this->task->getParser());
        $res = $method($input);
        if ($res == $this->output) {
            return true;
        }
        throw new Exception("Test failed, expected {$this->output} got $res");
    }
}