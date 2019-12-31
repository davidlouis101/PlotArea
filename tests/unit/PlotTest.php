<?php

use mohagames\PlotArea\utils\Location;
use PHPUnit\Framework\TestCase;

class PlotTest extends TestCase
{

    public function testPlotRegistration()
    {
        $plots = $this->generatePlot();

        $this->assertTrue(is_array($plots));
    }

    public function testPlotLookup()
    {
        $name = str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz");
        $this->registered_plot[] = ["name" => $name, "location" => new Location([["x" => 10, "y" => 20, "z" => 30], ["x" => 50, "y" => 80, "z" => 90]])];

        $position = ["x" => 30, "y" => 40, "z" => 45];

        $p_x = $position["x"];
        $p_y = $position["y"];
        $p_z = $position["z"];

        $plots = $this->generatePlot();
        foreach ($plots as $plot) {
            $location = $plot["location"];
            $loc = $location->calculateCoords();
            $pos1 = $loc->getPos1();
            $pos2 = $loc->getPos2();
            $res = $pos1["y"] == $pos2["y"];

            if (($p_x <= $pos1["x"] && $p_x >= $pos2["x"] && $p_z <= $pos1["z"] && $p_z >= $pos2["z"]) && (($p_y >= $pos1["y"] && $p_y < $pos2["y"]) || $res)) {
                $found_plot = $plot;
                break;
            }
        }

        $this->assertTrue(isset($plot));


    }

    public function generatePlot()
    {
        $name = str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz");
        $plots[] = ["name" => $name, "location" => new Location([["x" => 10, "y" => 20, "z" => 30], ["x" => 50, "y" => 80, "z" => 90]])];
        return $plots;
    }


}