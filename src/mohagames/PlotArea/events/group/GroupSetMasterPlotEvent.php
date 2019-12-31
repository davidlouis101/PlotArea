<?php


/*
  _____   _         _
 |  __ \ | |       | |      /\
 | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 */


namespace mohagames\PlotArea\events\group;


use mohagames\PlotArea\utils\Group;
use mohagames\PlotArea\utils\Plot;
use pocketmine\Player;

class GroupSetMasterPlotEvent extends GroupEvent
{


    private $group;
    private $masterplot;
    private $player;

    public function __construct(Group $group, Plot $masterplot, ?Player $executor = null)
    {
        $this->group = $group;
        $this->masterplot = $masterplot;
        $this->player = $executor;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function getPlot(): Plot
    {
        return $this->masterplot;
    }

}