<?php

namespace mohagames\PlotArea\events\group;

use mohagames\PlotArea\utils\Group;
use pocketmine\event\Event;

abstract class GroupEvent extends Event
{


    public abstract function getGroup(): Group;

}