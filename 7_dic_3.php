<?php

$data = file_get_contents('./7_dic.txt');

$regex = "/(.*) contain (.*)/";

$matches = [];
preg_match_all($regex, $data, $matches);


$bags = $matches[1];
$contained_bags = $matches[2];

$stack = ['shiny gold bag'];
$res = [];
$i = 0;
while (count($stack) > 0) {
    $bag = $bags[$i];
    $contains = $contained_bags[$i];

    if (strpos($contains, $stack[0]) !== false) {
        $stack[] = substr($bag, 0,-1); //remove last s
        $res[] = $bag;
    }

    $i++;

    if ($i == count($bags)){
        $i = 0;
        array_shift($stack);
    }
}

$final = array_unique($res);

echo count($final);