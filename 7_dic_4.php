<?php

// refactory secondo metodo di Sharpedge

$regex1 = "/(.*) contain (.*)/";
$regex2 = "/\d (.*?)s?(?:,|$)/";

$data = file_get_contents('./7_dic.txt');
$rows = explode("\n", $data);

$items = [];
foreach ($rows as $row) {
    preg_match_all($regex1, $row, $matches);
    preg_match_all($regex2, implode("\n", $matches[2]), $matches2);
    $items[$matches[1][0]] = $matches2[1];
}

$stack = ['shiny gold bag'];
$res = [];
$i = 0;
while (count($stack) > 0) {
    foreach ($items as $bag => $contains) {
        foreach ($contains as $singleContains) {
            if (strpos($singleContains, $stack[0]) !== false) {
                $stack[] = substr($bag, 0, -1); //remove last s
                $res[] = $bag;
            }
        }

    }

    $i++;

    array_shift($stack);
}

$final = array_unique($res);

echo count($final);