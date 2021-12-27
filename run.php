#!/usr/bin/php
<?php

$daysInAdvent = 25;

for ($i = 25; $i <= $daysInAdvent; $i++) {
    $namespace = "Day$i";
    if (file_exists(__DIR__ . "/inc/$namespace.php")) {
        require_once __DIR__ . "/inc/$namespace.php";

        $runnerClass = "\\$namespace\\Solver";
        /** @var Task $runner */
        $runner = new $runnerClass;
        foreach ($runner->tests() as $test) {
            $test->run();
        }
        echo "-- Day $i -- \n";
        echo $runner->print(Parser::parseLines("day$i.txt", $runner->getParser()));
    }
}