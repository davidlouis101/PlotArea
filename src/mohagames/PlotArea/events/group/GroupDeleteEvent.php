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

class GroupDeleteEvent extends GroupEvent implements Cancellable
{

    private $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

}