<?php

namespace mohagames\PlotArea\events;

use mohagames\PlotArea\utils\Plot;
use pocketmine\Player;

abstract class PlotEvent
{

    public abstract function getPlayer() : Player;

    public abstract function getPlot() : Plot;
}