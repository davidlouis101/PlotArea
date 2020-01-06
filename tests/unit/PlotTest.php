<?php

use PHPUnit\Framework\TestCase;

class PlotTest extends TestCase
{

    public function testPlotRegistration()
    {
        $plots = $this->generatePlot();

        $this->assertTrue(is_array($plots));
    }


    public function generatePlot()
    {
        $name = str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz");
        $plots[] = ["name" => $name];
        return $plots;
    }


}