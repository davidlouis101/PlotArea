<?php

/*
  _____   _         _
 |  __ \ | |       | |      /\
 | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 */

namespace mohagames\PlotArea\utils;


use pocketmine\math\Vector3;

class Location
{

    private $location;
    private $arrayedLocation;

    /**
     * De location class is een class gemaakt om het opslagen van de Locatie van Plots makkelijker te maken.
     * Er worden 2 coordinaten opgeslagen zodat het duidelijker is waar het Plot is.
     *
     * Location constructor.
     * @param array $location
     */
    public function __construct(array $location)
    {
        $this->arrayedLocation = $location;
        $this->location = [new Vector3($location[0]["x"], $location[0]["y"], $location[0]["z"]), new Vector3($location[1]["x"], $location[1]["y"], $location[1]["z"])];
    }


    /**
     * Dit returned een Array met de 2 Vector3 objects
     *
     * @return Vector3[]
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Please don't use this, this is for internal use only!
     *
     * @return array
     */
    public function getArrayedLocation(): array
    {
        return $this->arrayedLocation;
    }


    /**
     * Dit returned de 1ste vector3 van het Plot
     *
     * @return Vector3
     */
    public function getPos1(): Vector3
    {
        return $this->location[0];
    }

    /**
     * Dit returned de 2de Vector3 van het Plot
     *
     * @return Vector3
     */
    public function getPos2(): Vector3
    {
        return $this->location[1];
    }

    /**
     * Deze method returned een array met de coordinaten van het midden van het Plot
     *
     * @return Vector3
     */
    public function getCenter()
    {
        $location = $this->calculateCoords();
        $mid_x = ($location->getPos1()->getX() + $location->getPos2()->getX()) / 2;
        $mid_z = ($location->getPos1()->getZ() + $location->getPos2()->getZ()) / 2;
        $mid_y = ($location->getPos1()->getY() + $location->getPos2()->getY()) / 2;

        return new Vector3($mid_x, $mid_y, $mid_z);
    }

    /**
     * Deze method sorteert de coordinaten van groot naar klein zodat er makkelijker kan worden gerekend bij het zoeken naar een Plot.
     *
     * @return Location
     */
    public function calculateCoords()
    {

        $rechter_x = max($this->getPos1()->getX(), $this->getPos2()->getX());
        $linker_x = min($this->getPos1()->getX(), $this->getPos2()->getX());

        $rechter_z = max($this->getPos1()->getZ(), $this->getPos2()->getZ());
        $linker_z = min($this->getPos1()->getZ(), $this->getPos2()->getZ());

        $bovenste_y = max($this->getPos1()->getY(), $this->getPos2()->getY());
        $onderste_y = min($this->getPos1()->getY(), $this->getPos2()->getY());

        return new Location(array(array("x" => $rechter_x, "y" => $onderste_y, "z" => $rechter_z), array("x" => $linker_x, "y" => $bovenste_y, "z" => $linker_z)));
    }
}