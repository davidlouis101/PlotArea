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

class PlotRemoveMemberEvent extends PlotEvent implements Cancellable
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

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getPlot(): Plot
    {
        return $this->plot;
    }

    public function getMember(): string
    {
        return $this->member;
    }




}