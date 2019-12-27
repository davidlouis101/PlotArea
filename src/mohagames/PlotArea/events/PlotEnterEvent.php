<?php


/*
  _____   _         _
 |  __ \ | |       | |      /\
 | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 */

namespace mohagames\PlotArea\events;


use mohagames\PlotArea\utils\Plot;
use pocketmine\Player;

class PlotEnterEvent extends PlotEvent
{

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