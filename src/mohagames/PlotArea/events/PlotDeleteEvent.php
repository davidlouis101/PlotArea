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
use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlotDeleteEvent extends PlotEvent implements Cancellable
{


    private $player;
    private $plot;

    public function __construct(Plot $plot, Player $executor)
    {
        $this->player = $executor;
        $this->plot = $plot;
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