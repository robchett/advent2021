#!/usr/bin/php
<?php

$daysInAdvent = 25;

for ($i = 1; $i <= $daysInAdvent; $i++) {
    $namespace = "Day$i";
    if (file_exists(__DIR__ . "/inc/$namespace.php")) {
        require_once __DIR__ . "/inc/$namespace.php";

        $runnerClass = "\\$namespace\\Solver";
        /** @var Task $runner */
        $runner = new $runnerClass;
        foreach ($runner->tests() as $test) {
            $test->run();
        }
        $input = Parser::parseLines("day$i.txt", $runner->getParser());
        echo "-- Day $i -- \n";
        echo "Part 1:" . $runner->part1($input) . "\n";
        echo "Part 2:" . $runner->part2($input) . "\n";
    }
}