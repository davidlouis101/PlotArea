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

use mohagames\PlotArea\utils\Group;
use mohagames\PlotArea\utils\Plot;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlotJoinGroupEvent extends PlotEvent implements Cancellable
{

    private $plot;
    private $group;
    private $player;

    public function __construct(Plot $plot, Group $group, ?Player $executor = null)
    {
        $this->plot = $plot;
        $this->group = $group;
        $this->player = $executor;
    }


    public function getPlot(): Plot
    {
        return $this->plot;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

}