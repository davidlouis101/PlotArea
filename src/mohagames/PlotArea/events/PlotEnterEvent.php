<?php


namespace mohagames\PlotArea\events;


use mohagames\PlotArea\events\PlotEvent;
use mohagames\PlotArea\utils\Plot;
use pocketmine\Player;

class PlotEnterEvent extends PlotEvent{

    public $plot;
    public $player;

    public function __construct(Player $player, Plot $plot)
    {
        $this->plot = $plot;
        $this->player = $player;;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getPlot(): Plot
    {
        return $this->plot;
    }

}