<?php
/**
 * 13.45
 * 14.30 -> ho fatto setup di ambiente con tests
 */

require_once './vendor/autoload.php';


$data = file_get_contents('./20_dic.txt');
$tiles_raw = explode("\n\n", $data);

$tiles = [];


// init tiles
$tileMap = [];
foreach ($tiles_raw as $tile_raw) {
    $tile = explode("\n", $tile_raw);

    $newTile = [];
    foreach ($tile as $row) {
        $newTile[] = str_split($row);
    }

    $id = array_shift($newTile); // remove first line
    $id = str_replace(":", "", str_replace("Tile ", "", implode("", $id)));

    $newTile = new Tile($id, $newTile);
    $tiles[] = $newTile;
    $tileMap[$id] = $newTile;
}

// find common members
$associationMap = [];
$rightAssociationMap = [];
$bottomAssociationMap = [];

for ($i = 0; $i < count($tiles); $i++) {
    for ($j = 0; $j < count($tiles); $j++) {
        $t1 = $tiles[$i];
        $t2 = $tiles[$j];

        if ($t1->id != $t2->id) {

            if ($t1->hasCommonBorders($t2)) {
                $associationMap[$t1->id][] = $t2->id;
            }

            if ($t1->associatesRight($t2)) {
                $rightAssociationMap[$t1->id][] = $t2->id;
            }

            if ($t1->associatesBottom($t2)) {
                $bottomAssociationMap[$t1->id][] = $t2->id;
            }
        }
    }
}

// start to create matrix from corners
uasort($associationMap, function($a, $b) {
    return (count($a) <=> count($b));
});

$firstCornerID = array_keys($associationMap)[0];
$firstCorner = $tileMap[$firstCornerID];


// create matrix

$squareSize = sqrt(count($tiles));
$matrix[0][0] = $firstCorner;
$previous = $firstCorner;
$firstOfTheLine = $firstCorner;
$tileMapTemp = $tileMap;

$i = 0;
$j = 1;

while (true) {

    $rightFound = false;
    foreach ($tileMapTemp as $tile) {
        $res = $previous->attachRight($tile);

        if ($res) {
            $matrix[$i][$j] = $res;
            $previous = $res;
            $j++;
            $rightFound = true;
            break;
        }
    }

    if ($rightFound) {
        // remove the previous tile from $tiles
        unset($tileMapTemp[$previous->id]);
    } else {
        // not found, posso essere al bordo (lo capisco dal valore di $j)
        // oppure sono nella strada sbagliata, riavvolgo

        if ($j == $squareSize) {
            // sono al bordo, cambio riga
            $i++;

            if ($i == $squareSize && $j == $squareSize) {
                // FINITO
                break;
            }

                $j = 0;

            $bottomFound = false;
            foreach ($tileMapTemp as $tile) {
                $res = $firstOfTheLine->attachBottom($tile);

                if ($res) {
                    $matrix[$i][$j] = $res;
                    $firstOfTheLine = $res;
                    $previous = $res;
                    $bottomFound = true;
                    $j++;
                    break;
                }
            }

            if (!$bottomFound) {
                // devo riavvolgere e girare il primo
                $firstCorner->nextMode();
                $matrix[0][0] = $firstCorner;
                $i = 0;
                $j = 1;
                $previous = $firstCorner;
                $firstOfTheLine = $firstCorner;
                $tileMapTemp = $tileMap;
            }


        } else {
            // devo riavvolgere e girare il primo
            $firstCorner->nextMode();
            $matrix[0][0] = $firstCorner;
            $i = 0;
            $j = 1;
            $previous = $firstCorner;
            $firstOfTheLine = $firstCorner;
            $tileMapTemp = $tileMap;
        }

    }
}

$bigTile = "";

foreach ($matrix as $row) {

    for ($i = 0; $i < 10; $i++) {

        foreach ($row as $singleTile) {
            $tile = $singleTile->get();
            $bigTile .= " ".implode("", $tile[$i]);
        }

        $bigTile .= "\n";
    }

    $bigTile .="\n";
}


echo $bigTile;
