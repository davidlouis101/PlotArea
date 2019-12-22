<?php

use PHPUnit\Framework\TestCase;
use mohagames\PlotArea\utils\Location;
class Locationtest extends TestCase{

    public function testLocationCenter(){
        $loc = new Location([["x" => 10, "y" => 20,"z" => 30], ["x" => 50, "y" => 80, "z" => 90]]);
        $this->assertEquals(["x" => 30, "y" => 50, "z" => 60], $loc->getCenter());
    }
    public function testLocationPos1(){
        $loc = new Location([["x" => 10, "y" => 20,"z" => 30], ["x" => 50, "y" => 80, "z" => 90]]);
        $this->assertTrue(false);
    }

    public function testLocationPos2(){
        $loc = new Location([["x" => 10, "y" => 20,"z" => 30], ["x" => 50, "y" => 80, "z" => 90]]);
        $this->assertEquals(["x" => 50, "y" => 80, "z" => 90], $loc->getPos2());
    }

    public function testCoordCalculation(){
        $loc = new Location([["x" => 10, "y" => 20,"z" => 30], ["x" => 50, "y" => 80, "z" => 90]]);
        $this->assertEquals([["x" => 50, "y" => 20, "z" => 90], ["x" => 10, "y" => 80, "z" => 30]], $loc->calculateCoords()->getLocation());
    }

}