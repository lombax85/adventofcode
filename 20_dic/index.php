<?php
/**
 * 13.45
 * 14.30 -> ho fatto setup di ambiente con tests
 */

require_once './vendor/autoload.php';


$data = file_get_contents('./20_dic_full.txt');
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
        }
    }
    echo $i."\n";
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

    for ($i = 1; $i < 9; $i++) {

        foreach ($row as $singleTile) {
            $tile = $singleTile->get();
            $tileReduced = array_slice($tile[$i], 1, -1);
            $bigTile .= "".implode("", $tileReduced);
        }

        $bigTile .= "\n";
    }

    //$bigTile .="\n";
}

// create a new big tile

$bigTile = rtrim($bigTile);

$tile = explode("\n", $bigTile);

$newTile = [];
foreach ($tile as $row) {
    $newTile[] = str_split($row);
}

$newBigTile = new Tile(10000, $newTile);


$monster = '                  # 
#    ##    ##    ###
 #  #  #  #  #  #   ';

$candidateMonster = '/(?=(#....##....##....###))/'; // qui uso un lookahead altrimenti non becca gli overlapping https://stackoverflow.com/questions/22454032/preg-match-all-how-to-get-all-combinations-even-overlapping-ones
$candidateMonsterTop = '/..................#./';
$candidateMonsterBottom = '/.#..#..#..#..#..#.../';

$monsterMatches = 0;
for($mode = 0; $mode <= 7; $mode++) {
    $bigTileString = $newBigTile->print();

    $matches = [];
    $candidates = preg_match_all($candidateMonster, $bigTileString, $matches);

    echo "found ".count($matches[1])." candidate monsters \n";

    if (count($matches[1]) == 37) {
        echo "\n\n\n\n";
        echo $bigTileString;

        echo "\n\n\n\n";
    }

    if (count($matches) > 0) {
        foreach ($matches[1] as $match) {
            $pos = strpos($bigTileString, $match);
            $upperPos = $pos - count($newBigTile->get()[0]) - 1;
            $lowerPos = $pos + count($newBigTile->get()[0]) + 1;

            $upperSubstr = substr($bigTileString, $upperPos, strlen($candidateMonsterTop)-2);
            $lowerSubstr = substr($bigTileString, $lowerPos, strlen($candidateMonsterBottom)-2);

            $match1 = preg_match($candidateMonsterTop, $upperSubstr);
            $match2 = preg_match($candidateMonsterBottom, $lowerSubstr);

            if ($match1 && $match2)
                $monsterMatches++;
            else {
                echo $match1."\n";
                echo $match2."\n";
                echo $upperSubstr."\n";
                echo substr($bigTileString, $pos, strlen($candidateMonster)-2)."\n";
                echo $lowerSubstr."\n";
                echo "positions: pos $pos, upper $upperPos, lower $lowerPos \n";
            }
        }

        if ($monsterMatches) {
            //echo "we have $monsterMatches monster matches but see the next \n";
            //$monsterMatches = 0;
            break;
        }
    }


//    echo $bigTileString;
//    echo "\n\n";
    $newBigTile->nextMode();
    echo "next mode \n";

}


// monster ha 15 #
$monsterItems = substr_count($monster, '#');
$remove = $monsterItems * $monsterMatches;

$t = substr_count($bigTileString, '#');
$total = substr_count($bigTileString, '#') - $remove;

echo "found $monsterMatches monsters, there is a total of $t # and removing $remove \n";

echo $total;

/**
// C'è un errore nella regex perchè su queste righe (monster 37):

..##..#...#...#..#..#.#.#..#.###..............##...#.#.........#.......#......#.##..#...#...#..#
....#..#.##....##.#..###...#....#..#.##..##....#...##.#..#.....#....#.#..##...###....###..#####.
...#.#..##.#..#..##.#...#...#............#.....#...#...#...#.#.............##.##.##.##.#..#.#...

mi trova questo match per la riga centrale
 *
 *
#.#..##...###....###
 *
 * che però sopra e sotto non ha un monster.
 *
...#......#.##..#...
#.#..##...###....###
.......##.##.##.##.#
 *
 * Però 11 caratteri più avanti c'è un altro match (sono 2 match in overlap) e per qualche motivo non mi viene contato nei matches, sbaglio qualcosa nell'uso delle regex
 * Visivamente ho visto che è un monster valido
 *
....#.##..#...#...#.
#...###....###..####
.##.##.##.##.#..#.#.
 *
 * risolto poi con un lookahead come indicato qui
 * https://stackoverflow.com/questions/22454032/preg-match-all-how-to-get-all-combinations-even-overlapping-ones
 *
 *
 * */