<?php

namespace mohagames\PlotArea\events;

use mohagames\PlotArea\utils\Plot;
use pocketmine\event\Event;

abstract class PlotEvent extends Event
{

    public abstract function getPlot(): Plot;


}