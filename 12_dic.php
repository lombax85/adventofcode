<?php
/**
 * 15.27 - 16.00
 */
$data = file_get_contents('./12_dic.txt');
$rows = explode("\n", $data);

$ship = new Ship();

foreach ($rows as $row) {
    $action = substr($row, 0, 1);
    $number = substr($row, 1, strlen($row) - 1);
    $ship->doAction($action, $number);
}

echo $ship->manhattanDistance();


class Ship {

    public $x = 0;
    public $y = 0;

    public $direction = 1; // 0 north, 1 east, 2 south, 3 west, start by facing east

    public function manhattanDistance() {
        return abs($this->x) + abs($this->y);
    }

    public function doAction($action, $number) {
        switch ($action) {
            case "L":
                $this->direction = $this->calculateDirectionFromDirectionAndDegrees($this->direction, "L", $number);
                break;
            case "R":
                $this->direction = $this->calculateDirectionFromDirectionAndDegrees($this->direction, "R", $number);
                break;
            case "F":
                $this->moveForward($number);
                break;
            case "N":
                $prev = $this->direction;
                $this->direction = 0;
                $this->moveForward($number);
                $this->direction = $prev;
                break;
            case "E":
                $prev = $this->direction;
                $this->direction = 1;
                $this->moveForward($number);
                $this->direction = $prev;
                break;
            case "S":
                $prev = $this->direction;
                $this->direction = 2;
                $this->moveForward($number);
                $this->direction = $prev;
                break;
            case "W":
                $prev = $this->direction;
                $this->direction = 3;
                $this->moveForward($number);
                $this->direction = $prev;
                break;
        }
    }

    public function moveForward($units) {
        switch ($this->direction) {
            case 0:
                $this->y += $units;
                $this->accY += $units;
                break;
            case 1:
                $this->x += $units;
                $this->accX += $units;
                break;
            case 2:
                $this->y -= $units;
                $this->accY += $units;
                break;
            case 3:
                $this->x -= $units;
                $this->accX += $units;
                break;
        }
    }

    public function calculateDirectionFromDirectionAndDegrees($originalDirection, $newDirection, $degrees)
    {

        $add = 0;
        switch ($newDirection) {
            case "L":
                $add = -($degrees / 90);
                break;
            case "R":
                $add = ($degrees / 90);
                break;
        }

        if (!is_int($add)) {
            die('GRADI NON AMMESSI');
        }

        $newDirection = $originalDirection+$add;

        if ($newDirection < 0) {
            $newDirection = 4+$newDirection;
        }

        if ($newDirection > 3) {
            $newDirection = $newDirection - 4;
        }


        return $newDirection;
    }

}