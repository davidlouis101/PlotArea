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
use pocketmine\event\Cancellable;
use pocketmine\Player;

class GroupDeleteEvent extends GroupEvent implements Cancellable
{

    private $group;
    private $player;

    public function __construct(Group $group, ?Player $executor = null)
    {
        $this->group = $group;
        $this->player = $executor;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

}