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


class Location
{

    private $location;


    /**
     * De location class is een class gemaakt om het opslagen van de Locatie van Plots makkelijker te maken.
     * Er worden 2 coordinaten opgeslagen zodat het duidelijker is waar het Plot is.
     *
     * Location constructor.
     * @param array $location
     */
    public function __construct(array $location)
    {
        $this->location = $location;
    }


    /**
     * Dit returned een Array met de 2 coordinaten
     *
     * @return array
     */
    public function getLocation()
    {
        return $this->location;
    }


    /**
     * Dit returned de 1ste puntpositie van het Plot
     *
     * @return mixed
     */
    public function getPos1()
    {
        return $this->location[0];
    }

    /**
     * Dit returned de 2de puntpositie van het Plot
     *
     * @return mixed
     */
    public function getPos2()
    {
        return $this->location[1];
    }

    /**
     * Deze method returned een array met de coordinaten van het midden van het Plot
     *
     * @return array
     */
    public function getCenter()
    {
        $location = $this->calculateCoords();
        $mid_x = ($location->getPos1()["x"] + $location->getPos2()["x"]) / 2;
        $mid_z = ($location->getPos1()["z"] + $location->getPos2()["z"]) / 2;
        $mid_y = ($location->getPos1()["y"] + $location->getPos2()["y"]) / 2;
        return array("x" => $mid_x, "y" => $mid_y, "z" => $mid_z);
    }

    /**
     * Deze method sorteert de coordinaten van groot naar klein zodat er makkelijker kan worden gerekend bij het zoeken naar een Plot.
     *
     * @return Location
     */
    public function calculateCoords()
    {

        $rechter_x = max($this->getPos1()["x"], $this->getPos2()["x"]);
        $linker_x = min($this->getPos1()["x"], $this->getPos2()["x"]);

        $rechter_z = max($this->getPos1()["z"], $this->getPos2()["z"]);
        $linker_z = min($this->getPos1()["z"], $this->getPos2()["z"]);

        $bovenste_y = max($this->getPos1()["y"], $this->getPos2()["y"]);
        $onderste_y = min($this->getPos1()["y"], $this->getPos2()["y"]);

        return new Location(array(array("x" => $rechter_x,"y" => $onderste_y, "z" => $rechter_z), array("x" => $linker_x, "y" => $bovenste_y, "z" => $linker_z)));
    }
}