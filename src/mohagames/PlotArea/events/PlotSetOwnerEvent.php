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

class PlotSetOwnerEvent extends PlotEvent implements Cancellable
{

    protected $player;
    protected $owner;
    protected $plot;


    public function __construct(Plot $plot, Player $player, string $owner)
    {
        $this->player = $player;
        $this->owner = $owner;
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

    public function getOwner() : string
    {
        return $this->owner;
    }

}