<?php

class Tile
{

    protected $originalTile = [];
    protected $tile = [];
    public $id = 0;


    public $modes = []; // 0 original, 1 +90 clockw, 2 +180, 3 +270, 4 original flip hr, 5 +90, 6 +180, 7 +270
    public $sides = []; // array of arrays, [$mode][$side]. 0 top, 1 right, 2 bottom, 3 left
    /**
     * Association contains
     *
     * [
     *  'this_id' => xxx,
     * 'other_id' => xxx,
     * 'this_mode' => xxx,
     * 'other_mode' => xxx,
     * 'this_side' => xxx,
     * 'other_side' => xxx,
     * ]
     */
    public $associations = [];
    public $rightAssociations = [];
    public $bottomAssociations = [];

    protected $actualMode = 0;
    protected $blocked = false;


    public function __construct(int $id, array $tile)
    {
        $this->id = $id;
        $this->tile = $tile;
        $this->originalTile = $this->tile;

        for($i = 0; $i <= 7; $i++) {
            $this->modes[$i] = $this->tile;
            $this->borders[$i] = $this->createBorders();
            $this->nextMode();
        }
    }

    public function get(): array {
        return $this->tile;
    }

    public function print() : string {
        $output = "";
        foreach ($this->tile as $row) {
            $output .= implode("", $row)."\n";
        }

        $output = rtrim($output);

        return $output;
    }

    public function getBorders(int $mode) : array
    {
        return $this->borders[$mode];
    }


    public function isCorner() {
        if (count($this->associations) == 2)
            return true;
        return false;
    }

    public function isSide() {
        if (count($this->associations) == 3)
            return true;
        return false;
    }

    public function isCentral() {
        if (count($this->associations) == 4)
            return true;
        return false;
    }

    public function isBlocked() : bool
    {
        return $this->blocked;
    }

    /**
     * @return int
     */
    public function getActualMode(): int
    {
        return $this->actualMode;
    }


    protected function rotate90() {
        if (!$this->blocked) {
            $matrix = $this->tile;
            $height = count($matrix);
            $width = count($matrix[0]);
            $mat90 = array();

            for ($i = 0; $i < $width; $i++) {
                for ($j = 0; $j < $height; $j++) {
                    $mat90[$height - $i - 1][$j] = $matrix[$height - $j - 1][$i];
                }
            }

            $mat90 = array_values($mat90); // reset keys

            $this->tile = $mat90;

        } else {
            throw new \Exception('Trying to rotate a locked tile');
        }
    }

    protected function flipHorizontal() {
        if (!$this->blocked) {
            $tile = $this->tile;
            $newTile = [];
            foreach ($tile as $tilerow) {
                $newTile[] = array_reverse($tilerow);
            }
            $this->tile = $newTile;
        } else {
            throw new \Exception('Trying to flip a locked tile');
        }
    }

    protected function nextMode() : int {

        if (!$this->blocked) {
            if ($this->actualMode == 7)
            {
                // come back to 0 original tile
                $this->tile = $this->originalTile;
                $this->actualMode = 0;
            } else if ($this->actualMode == 3){
                // come back to 0 original tile and then flip
                $this->tile = $this->originalTile;
                $this->flipHorizontal();
                $this->actualMode++;
            } else {
                $this->rotate90();
                $this->actualMode++;
            }

            return $this->actualMode;
        } else {
            throw new \Exception('Trying to next mode a locked tile');
        }

    }

    protected function createBorders() : array
    {
        $version = $this->tile;
        $borders = [];
        $borders[0] = $version[0];                      // top
        $borders[1] = [];                               // right (top-to-bottom)
        $borders[2] = $version[count($version)-1];      // bottom
        $borders[3] = [];                               // left (top-to-bottom)

        for ($i = 0; $i < count($version); $i++) {
            $borders[3][] = $version[$i][0];
            $borders[1][] = $version[$i][count($version)-1];
        }

        return $borders;

    }

    public function getRightTile() {
        $association = array_filter($this->rightAssociations, function ($v) {
            if ($v['this_side'] == 1 && $v['this_mode'] == $this->actualMode)
                return true;
            return false;
        });

        $association = array_values($association);

        if (count($association) > 1)
        {
            throwException('Troppe associazioni a destra, errore');
        }

        if (count($association) == 1)
        {
            return $association[0]['other_id'];
        } else {
            return null;
        }

    }

    public function getBottomTile() {
        $association = array_filter($this->bottomAssociations, function ($v) {
            if ($v['this_side'] == 2 && $v['this_mode'] == $this->actualMode)
                return true;
            return false;
        });

        if (count($association) > 1)
        {
            throwException('Troppe associazioni sotto, errore');
        }

        if (count($association) == 1)
        {
            return $association[0]['other_id'];
        } else {
            return null;
        }

    }


    public function hasCommonBorders($newTile) {

        /**
         * @var $t1 Tile
         * @var $t2 Tile
         */
        $t1 = $this;
        $t2 = $newTile;

        $actualAssociations = 0;

        for($t1Mode = 0; $t1Mode <= 7; $t1Mode++) {
            for($t2Mode = 0; $t2Mode <= 7; $t2Mode++) {

                for ($i = 0; $i <= 3; $i++) {
                    for ($j = 0; $j <= 3; $j++) {
                        $t1Borders = $t1->getBorders($t1Mode);
                        $t2Borders = $t2->getBorders($t2Mode);
                        if ($t1Borders[$i] == $t2Borders[$j]) {
                            $t1->associations[] = [
                                'this_id' => $t1->id,
                                'other_id' => $t2->id,
                                'this_mode' => $t1Mode,
                                'other_mode' => $t2Mode,
                                'this_side' => $i,
                                'other_side' => $j,
                            ];

                            $actualAssociations++;

                            if ($t1->id == "1951" && $t2->id == "2729") {
                                echo "";
                            }
//                            $t2->associations[] = [
//                                'this_id' => $t2->id,
//                                'other_id' => $t1->id,
//                                'this_mode' => $t2Mode,
//                                'other_mode' => $t1Mode,
//                                'this_side' => $j,
//                                'other_side' => $i,
//                            ];

                            // block them
                            $t1->actualMode = $t1Mode;
                            $t1->tile = $t1->modes[$t1Mode];
                            $t2->actualMode = $t2Mode;
                            $t2->tile = $t2->modes[$t2Mode];
                            $t1->blocked = true;
                            $t2->blocked = true;

                            return true;
                        }
                    }
                }
            }
        }

//        if ($actualAssociations > 0)
//            return true;

        return false;
    }


    public function associatesRight($newTile) {

        /**
         * @var $t1 Tile
         * @var $t2 Tile
         */
        $t1 = $this;
        $t2 = $newTile;

        $actualAssociations = 0;

        for($t1Mode = 0; $t1Mode <= 7; $t1Mode++) {
            for($t2Mode = 0; $t2Mode <= 7; $t2Mode++) {

                $i = 1; // only right side

                for ($j = 0; $j <= 3; $j++) {
                    $t1Borders = $t1->getBorders($t1Mode);
                    $t2Borders = $t2->getBorders($t2Mode);
                    if ($t1Borders[$i] == $t2Borders[$j]) {
                        $t1->rightAssociations[] = [
                            'this_id' => $t1->id,
                            'other_id' => $t2->id,
                            'this_mode' => $t1Mode,
                            'other_mode' => $t2Mode,
                            'this_side' => $i,
                            'other_side' => $j,
                        ];

                        // block them
                        $t1->actualMode = $t1Mode;
                        $t1->tile = $t1->modes[$t1Mode];
                        $t2->actualMode = $t2Mode;
                        $t2->tile = $t2->modes[$t2Mode];
                        $t1->blocked = true;
                        $t2->blocked = true;

                        return true;
                    }
                }

            }
        }

        return false;
    }

    public function associatesBottom($newTile) {

        /**
         * @var $t1 Tile
         * @var $t2 Tile
         */
        $t1 = $this;
        $t2 = $newTile;

        $actualAssociations = 0;

        for($t1Mode = 0; $t1Mode <= 7; $t1Mode++) {
            for($t2Mode = 0; $t2Mode <= 7; $t2Mode++) {

                $i = 2; // only bottom side

                for ($j = 0; $j <= 3; $j++) {
                    $t1Borders = $t1->getBorders($t1Mode);
                    $t2Borders = $t2->getBorders($t2Mode);
                    if ($t1Borders[$i] == $t2Borders[$j]) {
                        $t1->bottomAssociations[] = [
                            'this_id' => $t1->id,
                            'other_id' => $t2->id,
                            'this_mode' => $t1Mode,
                            'other_mode' => $t2Mode,
                            'this_side' => $i,
                            'other_side' => $j,
                        ];

                        // block them
                        $t1->actualMode = $t1Mode;
                        $t1->tile = $t1->modes[$t1Mode];
                        $t2->actualMode = $t2Mode;
                        $t2->tile = $t2->modes[$t2Mode];
                        $t1->blocked = true;
                        $t2->blocked = true;

                        return true;
                    }
                }

            }
        }

        return false;
    }


}