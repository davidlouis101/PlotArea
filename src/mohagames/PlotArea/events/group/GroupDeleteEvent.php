<?php

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