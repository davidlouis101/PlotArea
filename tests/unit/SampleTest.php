<?php

use PHPUnit\Framework\TestCase;
use mohagames\PlotArea\utils\Location;
class SampleTest extends TestCase{

    public function testPlot(){
        $loc = new Location([["x" => 10, "y" => 20,"z" => 30], ["x" => 50, "y" => 80, "z" => 90]]);
        $this->assertEquals([30, 60], $loc->getCenter());
    }
}