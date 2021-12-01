<?php

require_once 'Stage.php';

/**
 *  @template Tin
 *  @template Tout
 */
class Test {

    /**
     * @param Task<Tin, Tout> $task
     * @param Tin $input
     * @param Tout $output
     * @param Stage $stage
     */
    public function __construct(
        readonly public Task $task,
        readonly public mixed $input,
        readonly public mixed $output,
        readonly public Stage $stage,
    ) {

    }

    public function run(): bool {
        $res = match ($this->stage) {
            Stage::Stage1 => $this->task->part1($this->input),
            Stage::Stage2 => $this->task->part2($this->input),
        };
        if ($res == $this->output) {
            return true;
        }
        throw new Exception('Test failed');
    }
}