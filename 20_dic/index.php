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

$i = 0;
$j = 1;

while (true) {

    $rightTileID = $previous->getRightTile();


    if ($rightTileID) {
        $rightTile = $tileMap[$rightTileID];
        $matrix[$i][$j] = $rightTile;
        $previous = $rightTile;
        $j++;
    } else {
        // go to next line
        $j = 0;
        $i++;

        $bottomTileID = $firstOfTheLine->getBottomTile();

        if ($bottomTileID) {
            $bottomTile = $associationMap[$bottomTileID];
            $matrix[$i][$j] = $bottomTile;
            $firstOfTheLine = $bottomTile;
        } else {
            break; // finish
        }
    }
}


for ($i = 0; $i < $squareSize; $i++) {
    for ($j = 0; $j < $squareSize; $j++) {




    }
}

echo "test";
