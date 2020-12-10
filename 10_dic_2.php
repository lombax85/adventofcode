<?php

/**
 * 11.27 - 11.36
 *
 *
 */
$data = file_get_contents('./10_dic.txt');
$rows = explode("\n", $data);

$min = min($rows);
$max = max($rows);
$embedded = $max + 3;


$rows[] = 0;
sort($rows);
$rows[] = $embedded;

$divided = [];

$divIndex = 0;
for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $divided[$divIndex][] = $row;
    $next = $rows[$i+1];

    if ($next-$row == 3) {
        $divIndex++;
    }
}

$acc = 1;
foreach ($divided as $sub_arr) {
    $acc = $acc * find_valid_combinations(count($sub_arr));
}

echo $acc;

function find_valid_combinations2($n) {
    // 1 2 3 4 5
    // 1 *2 *3 4 *5 6
    // 1 *2 *3 4 *5 *6 7
    // 1 *2 *3 4 *5 6 *7 8
    // 1 *2 *3 4 *5 *6 7 *8 9
    // 1 2* 3* 4 5* 6* 7 8* 9* 10
    // 1 2* 3* 4 5* 6* 7 8* 9* 10 11
    // 1 2* 3 4* 5* 6 7* 8* 9 10* 11
    // 1 2* 3 4* 5* 6 7* 8* 9 10* 11* 12
    // 1 2* 3* 4 5* 6* 7 8* 9* 10 11* 12

    // quanti ne posso togliere?
    //3 su 5 -> 2            3
    //4 su 6 -> 3            3
    //5 su 7 -> 4            3
    //6 su 8 -> 4            4
    //7 su 9 -> 5            4
    //8 su 10 -> 6           4
    //9 su 11 -> 6           5
    //10 su 12 -> 7           5

}

function can_remove($n) {
    if ($n % 2 == 0) {
        // pari


    } else {
        // dispari
    }
}

function find_valid_combinations($n) {
    switch ($n) {
        case 0:
            return 1;
            break;
        case 1:
            return 1;
            break;
        case 2:
            return 1;
            break;
        case 3:
            return 2;
            break;
        case 4:
            return 4;
            break;
        case 5:
            return 7;
            break;
    }
}

echo "";
