<?php
/**
 * 11.23
 *
 */
$data = file_get_contents('./11_dic.txt');
$rows = explode("\n", $data);

$seats = [];

foreach ($rows as $row) {
    $arr = str_split($row);
    $seats[] = $arr;
}

/**
 * L seat
 * . floor
 * # seat occupied
 *
 * ? candidate for seat
 * * candidate for empty
 *
 */

$prevCount = 0;
while (true) {
    for ($x = 0; $x < count($seats); $x++) {
        for ($y = 0; $y < count($seats[0]); $y++) {
            if ($seats[$x][$y] == "L" && surroundingFree($x, $y, $seats)) {
                $seats[$x][$y] = "?";
            }

            if ($seats[$x][$y] == "#" && fiveMoreSeatsOccupied($x, $y, $seats)) {
                $seats[$x][$y] = "*";
            }

        }
    }

    applySeat($seats);
    $count = countSeat($seats);

    if ($count == $prevCount) {
        echo $count;
        break;
    } else {
        $prevCount = $count;
    }
}

// count



function countSeat($seats) {
    $count = 0;
    for ($x = 0; $x < count($seats); $x++) {
        for ($y = 0; $y < count($seats[0]); $y++) {
            if ($seats[$x][$y] == "#") {
                $count++;
            }

        }
    }

    return $count;
}

function applySeat(&$seats) {
    for ($x = 0; $x < count($seats); $x++) {
        for ($y = 0; $y < count($seats[0]); $y++) {
            if ($seats[$x][$y] == "?") {
                $seats[$x][$y] = "#";
            }

            if ($seats[$x][$y] == "*") {
                $seats[$x][$y] = "L";
            }
        }
    }
}

function fiveMoreSeatsOccupied($x, $y, $seats) {
    $freePositions = [];

    for ($i = 0; $i <= 7; $i++) {
        $seat = getFirstSeatInDirection($x, $y, $seats, $i);

        if ($seat == null) {
            $freePositions[] = true;
        } else {
            if (in_array($seat, ["L", ".", "?"])) {
                $freePositions[] = true;
            }
        }


    }

    if (count($freePositions) <= 3) {
        return true;
    } else {
        return false;
    }
}

function surroundingFree($x, $y, $seats) {
    $freePositions = [];

    for ($i = 0; $i <= 7; $i++) {
        $seat = getFirstSeatInDirection($x, $y, $seats, $i);

        if ($seat == null) {
            $freePositions[] = true;
        } else {
            if (in_array($seat, ["L", ".", "?"]) )
            {
                $freePositions[] = true;
            }
        }
    }

    if (count($freePositions) == 8) {
        return true;
    } else {
        return false;
    }
}

function getFirstSeatInDirection($x, $y, $seats, $direction) {

    if ($x == 0 && $y == 2) {
        echo "";
    }
    $seat = "";

    $cnt = 0;
    while ($seat == "") {
        $nx = $x;
        $ny = $y;

        $newPosition = Position::get($nx, $ny, $seats, $direction);

        if ($newPosition == null) {
            return null;
        }

        $newSeat = $seats[$newPosition->x][$newPosition->y];
        if (in_array($newSeat, ['#', 'L'])) {
            return $newSeat;
        } else if ($newSeat == "?") {
            return "L";
        } else if ($newSeat == "*") {
            return "#";
        } else if ($newSeat == ".") {
            //$newPosition = Position::get($newPosition->x, $newPosition->y, $seats, $direction);
            if ( $newPosition == null) {
                return null;
            } else {
                $x = $newPosition->x;
                $y = $newPosition->y;
            }
        }

        $cnt++;

        if ($cnt > 100) {
            echo "";
        }
    }
}

class Position {

    public $x;
    public $y;

    const UPPER = 0;
    const RIGHT = 1;
    const LOWER = 2;
    const LEFT = 3;
    const UPPERRIGHT = 4;
    const UPPERLEFT = 5;
    const LOWERRIGHT = 6;
    const LOWERLEFT = 7;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }


    public static function get($x, $y, $arr, $newPosition) {
        switch ($newPosition) {
            case Position::UPPER:
                $nx = $x-1;
                $ny = $y;
                break;
            case Position::LOWER:
                $nx = $x+1;
                $ny = $y;
                break;
            case Position::LEFT:
                $nx = $x;
                $ny = $y-1;
                break;
            case Position::RIGHT:
                $nx = $x;
                $ny = $y+1;
                break;
            case Position::UPPERLEFT:
                $nx = $x-1;
                $ny = $y-1;
                break;
            case Position::UPPERRIGHT:
                $nx = $x-1;
                $ny = $y+1;
                break;
            case Position::LOWERLEFT:
                $nx = $x+1;
                $ny = $y-1;
                break;
            case Position::LOWERRIGHT:
                $nx = $x+1;
                $ny = $y+1;
                break;
        }


        if ($nx < 0 || $nx >= count($arr))
            return null;

        if ($ny < 0 || $ny >= count($arr[0]))
            return null;

        return new Position($nx, $ny);

    }
}


class Seat
{
    public $kind;

    public $x;
    public $y;

    public function __construct($x, $y, $kind)
    {
        $this->x = $x;
        $this->y = $y;
        $this->kind = $kind;
        self::$seats[$x][$y] = $this;
    }

    static $seats = [];
    public static function getSeat($x, $y) {
        return self::$seats[$x][$y] ?? null;
    }


}

