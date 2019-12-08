<?php

namespace mohagames\PlotArea\events;

use mohagames\PlotArea\utils\Plot;
use pocketmine\Player;

class PlotSetOwnerEvent extends PlotEvent
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