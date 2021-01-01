<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TileTest extends TestCase
{
    public function initTile() : SubTile
    {
        $tile_raw = file_get_contents('./tests/singleTile.txt');
        $tile = explode("\n", $tile_raw);
        $newTile = [];
        foreach ($tile as $row) {
            $newTile[] = str_split($row);
        }

        $id = array_shift($newTile); // remove first line
        $id = intval(str_replace(":", "", str_replace("Tile ", "", implode("", $id))));

        $newConstructedTile = new SubTile($id, $newTile);
        return $newConstructedTile;
    }

    public function initRotatedTile() : SubTile
    {
        $tile_raw = file_get_contents('./tests/singleTileRotated.txt');
        $tile = explode("\n", $tile_raw);
        $newTile = [];
        foreach ($tile as $row) {
            $newTile[] = str_split($row);
        }

        $id = array_shift($newTile); // remove first line
        $id = intval(str_replace(":", "", str_replace("Tile ", "", implode("", $id))));

        $newConstructedTile = new SubTile($id, $newTile);
        return $newConstructedTile;
    }

    public function initTileInMode(int $mode) : SubTile
    {
        $tile_raw = file_get_contents('./tests/modes/singleTile'.$mode.'.txt');
        $tile = explode("\n", $tile_raw);
        $newTile = [];
        foreach ($tile as $row) {
            $newTile[] = str_split($row);
        }

        $id = array_shift($newTile); // remove first line
        $id = intval(str_replace(":", "", str_replace("Tile ", "", implode("", $id))));

        $newConstructedTile = new SubTile($id, $newTile);
        return $newConstructedTile;
    }

    public function testIsCorner(): void
    {
        $tile = $this->initTile();
        $tile->associations[] = ['dummy1'];
        $tile->associations[] = ['dummy2'];

        $this->assertEquals($tile->isCorner(), true);
    }

    public function testIsSide(): void
    {
        $tile = $this->initTile();
        $tile->associations[] = ['dummy1'];
        $tile->associations[] = ['dummy2'];
        $tile->associations[] = ['dummy3'];

        $this->assertEquals($tile->isSide(), true);
    }

    public function testIsCentral(): void
    {
        $tile = $this->initTile();
        $tile->associations[] = ['dummy1'];
        $tile->associations[] = ['dummy2'];
        $tile->associations[] = ['dummy3'];
        $tile->associations[] = ['dummy4'];

        $this->assertEquals($tile->isCentral(), true);
    }

//    public function testConstructorAndGet() : void
//    {
//        $this->assertEquals($this->initTile()->get(), $newTile);
//    }

    public function testPrint() : void
    {
        $tile_raw = file_get_contents('./tests/singleTileNoID.txt');
        $this->assertEquals($this->initTile()->print(), $tile_raw);
    }

    public function testRotate90() : void
    {
        $tile = $this->initTile();
        $rotatedTile = $this->initRotatedTile();

        $tile->rotate90();

        $this->assertEquals($tile->print(), $rotatedTile->print());
    }

    public function testFlipHorizontal() : void
    {
        $tile = $this->initTile();
        $rotatedTile = $this->initTileInMode(4);

        $tile->flipHorizontal();

        $this->assertEquals($tile->print(), $rotatedTile->print());
    }

    public function testModes() : void
    {
        $tile = $this->initTile();
        $originalTileString = $tile->print();

        for($i = 0; $i <= 7; $i++) {
            $compareTile = $this->initTileInMode($i);

            $this->assertEquals($tile->getActualMode(), $i, "Modes are different. \$i is $i and mode is ".$tile->getActualMode());
            $this->assertEquals($compareTile->print(), $tile->print(), "Mode: $i");
            $tile->nextMode();
        }

        $this->assertEquals($tile->getActualMode(), 0, "Modes are different. It should be 0 but is ".$tile->getActualMode());
        $this->assertEquals($originalTileString, $tile->print(), "Error from mode 7 to 0");

    }

    public function testBlocked() : void
    {
        $tile = $this->initTile();
        $tile->blocked = true;

        $this->expectExceptionMessage('Trying to next mode a locked tile');
        $tile->nextMode();

        $this->expectExceptionMessage('Trying to rotate a locked tile');
        $tile->rotate90();

        $this->expectExceptionMessage('Trying to flip a locked tile');
        $tile->flipHorizontal();
    }

    public function testGetBorders() : void {
        $tile = $this->initTile();

        $borders = $tile->createBorders();
        $expectedBorders = [
            str_split('..##.#..#.'), // top
            str_split('...#.##..#'), // right
            str_split('..###..###'), // bottom
            str_split('.#####..#.'), // left
        ];

        $this->assertEquals($borders, $expectedBorders);
    }

    public function testGetRotatedBorders() : void {
        $tile = $this->initTile();

        for($i = 0; $i <= 7; $i++) {
            $borders = $tile->getBorders($i);

            $this->assertEquals($tile->createBorders(), $tile->getBorders($i), "Borders doesn't match for $i");
            $tile->nextMode();
        }
    }

    public function testHasCommonBorders() : void {
        $tile = $this->initTileInMode(0);
        $flipped = $this->initTileInMode(4);

        $v = $tile->hasCommonBorders($flipped);

        $this->assertEquals(count($tile->associations), 1);
    }

}

class SubTile extends Tile {

    public $blocked = false;

    public function nextMode(): int
    {
        return parent::nextMode();
    }

    public function rotate90()
    {
        parent::rotate90();
    }

    public function flipHorizontal()
    {
        parent::flipHorizontal();
    }

    public function createBorders(): array
    {
        return parent::createBorders();
    }

}
