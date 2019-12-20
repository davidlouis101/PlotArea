<?php


namespace src\mohagames\PlotArea\events;


use mohagames\PlotArea\events\PlotEvent;
use mohagames\PlotArea\utils\Plot;
use pocketmine\Player;

class PlotDeleteEvent extends PlotEvent
{

    private $player;
    private $plot;

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getPlot(): Plot
    {
        return $this->plot;
    }

}