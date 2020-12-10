<?php

/**
 * 11.27 - 11.36
 *
 *
 */
$data = file_get_contents('./10_dic.txt');
$rows = explode("\n", $data);
$rows[] = 0;
sort($rows);

foreach ($rows as $row) {
    $numbers[] = [
        'n' => $row,
        'alts' => 0
    ];
}

$numbers[count($numbers)-1]['alts'] = 1;

for ($i = count($numbers)-1; $i >= 0; $i--) {
    if ($i >=1 && (($numbers[$i]['n']-$numbers[$i - 1]['n']) <= 3)) {
        $numbers[$i - 1]['alts'] += $numbers[$i]['alts'];
    }
    if ($i >=2 && (($numbers[$i]['n']-$numbers[$i - 2]['n']) <= 3)) {
        $numbers[$i - 2]['alts'] += $numbers[$i]['alts'];
    }
    if ($i >=3 && (($numbers[$i]['n']-$numbers[$i - 3]['n']) <= 3)) {
        $numbers[$i - 3]['alts'] += $numbers[$i]['alts'];
    }
}



echo $numbers[0]['alts'];

$dict = [];
$dict[0] = 1;
for ($i = 0; $i < count($rows); $i++) {
    $n = $rows[$i];
    if ($i != 0) {
        $dict[$n] = $dict[$n - 1] + $dict[$n - 2] + $dict[$n - 3];
    }
}

echo "";