<?php


namespace mohagames\PlotArea\utils;
/*
 * Deze class is deprecated en zal vervangen worden omdat die kut is
 * Dus probeer deze class zoveel mogelijk te vermijden en gewoon gebruik te maken van Vector3
 */

class Location{

    private $location;

    public function __construct(array $location)
    {
        $this->location = $location;
    }

    public function getLocation(){
        return $this->location;
    }

    public function getPos1(){
        return $this->location[0];

    }
    public function getPos2(){
        return $this->location[1];
    }

    public function getCenter(){
        $location = $this->calculateCoords();
        $mid_x = ($location->getPos1()[0] + $location->getPos2()[0]) / 2;
        $mid_z = ($location->getPos1()[1] + $location->getPos2()[1]) / 2;
        return array($mid_x, $mid_z);
    }

    /*
     * Deze method zoveel mogelijk vermijden aangezien die `protected` wordt.
     */
    public function calculateCoords(){

        $rechter_x = max($this->getPos1()["x"], $this->getPos2()["x"]);
        $linker_x = min($this->getPos1()["x"], $this->getPos2()["x"]);

        $rechter_z = max($this->getPos1()["z"], $this->getPos2()["z"]);
        $linker_z = min($this->getPos1()["z"], $this->getPos2()["z"]);

        $bovenste_y = max($this->getPos1()["y"], $this->getPos2()["y"]);
        $onderste_y = min($this->getPos1()["y"], $this->getPos2()["y"]);

        return new Location(array(array("x" => $rechter_x,"y" => $onderste_y, "z" => $rechter_z), array("x" => $linker_x, "y" => $bovenste_y, "z" => $linker_z)));
    }
}