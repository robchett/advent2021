#!/usr/bin/php
<?php

$daysInAdvent = 25;

for ($i = 1; $i <= $daysInAdvent; $i++) {
    $class = "Day$i";
    if (file_exists(__DIR__ . "/inc/$class.php")) {
        require_once __DIR__ . "/inc/$class.php";
        /** @var Task $runner */
        $runner = new $class;
        foreach ($runner->tests() as $test) {
            $test->run($runner);
        }
        $input = Parser::parseLines($i, $runner->getParser());
        echo "-- Day $i -- \n";
        echo "Part 1:" . $runner->part1($input) . "\n";
        echo "Part 2:" . $runner->part2($input) . "\n";
    }
}