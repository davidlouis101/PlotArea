<?php

namespace mohagames\PlotArea\events;

use mohagames\PlotArea\utils\Plot;
use pocketmine\Player;

class PlotAddMemberEvent extends PlotEvent
{

    protected $player;
    protected $member;
    protected $plot;

    public function __construct(Plot $plot, Player $player, string $member)
    {
        $this->player = $player;
        $this->member = $member;
        $this->plot = $plot;
    }


    public function getPlayer() : Player{
        return $this->player;
    }

    public function getPlot(): Plot
    {
        return $this->plot;
    }

    public function getMember() : string{
     return $this->member;
    }


}