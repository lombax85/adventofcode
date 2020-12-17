<?php
/**
 * 19.39 -
 */
$data = file_get_contents('./adventofcode2020/SharpEdgeMarshall/puzzle12.txt');
$rows = explode("\n", $data);

$ship = new ShipWithWaypoint();
$l = 0;
foreach ($rows as $row) {
    $action = substr($row, 0, 1);
    $number = substr($row, 1, strlen($row) - 1);

    if ($row == "L90") {
        $l++;
        if ($l == 9) {

            echo "";
        }
    }
    $ship->doAction($action, $number);
}

$dist = $ship->manhattanDistance();
echo $dist;


class ShipWithWaypoint {

    public $shipX = 0;
    public $shipY = 0;

    public $waypointX = 10;
    public $waypointY = 1;

    public function manhattanDistance() {
        return abs($this->shipX) + abs($this->shipY);
    }

    public function doAction($action, $number) {
        switch ($action) {
            case "L":
                $this->rotateWayPointAroundShip($action, $number);
                break;
            case "R":
                $this->rotateWayPointAroundShip($action, $number);
                break;
            case "F":
                $this->moveShipToWaypoint($number);
                break;
            case "N":
                $this->waypointY += $number;
                break;
            case "E":
                $this->waypointX += $number;
                break;
            case "S":
                $this->waypointY -= $number;
                break;
            case "W":
                $this->waypointX -= $number;
                break;
        }
    }


    public function moveShipToWaypoint($times) {

        for ($i = 0; $i < $times; $i++) {
            $deltaX = $this->waypointX - $this->shipX;
            $deltaY = $this->waypointY - $this->shipY;

            $this->shipX = $this->waypointX;
            $this->shipY = $this->waypointY;

            $this->waypointX += $deltaX;
            $this->waypointY += $deltaY;
        }

    }

    public function rotateWayPointAroundShip($direction, $degrees) {
        $times = $degrees / 90;
        for ($i = 0; $i < $times; $i++) {

            $relativeX = $this->waypointX - $this->shipX;
            $relativeY = $this->waypointY - $this->shipY;

            switch ($direction) {
                case "L":

                    if ($relativeX >= 0 && $relativeY >= 0) {
                        $bk = $relativeY;
                        $relativeY = $relativeX;
                        $relativeX = -$bk;
                        break;
                    }


                    if ($relativeX >= 0 && $relativeY < 0) {
                        $bk = $relativeX;
                        $relativeX = -$relativeY;
                        $relativeY = $bk;
                        break;
                    }


                    if ($relativeX < 0 && $relativeY < 0) {
                        $bk = $relativeY;
                        $relativeY = $relativeX;
                        $relativeX = -$bk;
                        break;
                    }


                    if ($relativeX < 0 && $relativeY >= 0) {
                        $bk = $relativeX;
                        $relativeX = -$relativeY;
                        $relativeY = $bk;
                        break;
                    }


                    break;
                case "R":
                    if ($relativeX >= 0 && $relativeY >= 0) {
                        $bk = $relativeX;
                        $relativeX = $relativeY;
                        $relativeY = -$bk;
                        break;
                    }


                    if ($relativeX >= 0 && $relativeY < 0) {
                        $bk = $relativeY;
                        $relativeY = -$relativeX;
                        $relativeX = $bk;
                        break;
                    }


                    if ($relativeX < 0 && $relativeY < 0) {
                        $bk = $relativeX;
                        $relativeX = $relativeY;
                        $relativeY = -$bk;
                        break;
                    }


                    if ($relativeX < 0 && $relativeY >= 0) {
                        $bk = $relativeY;
                        $relativeY = -$relativeX;
                        $relativeX = $bk;
                        break;
                    }


                    break;
            }

            $this->waypointX = $relativeX + $this->shipX;
            $this->waypointY = $relativeY + $this->shipY;

        }
    }



}