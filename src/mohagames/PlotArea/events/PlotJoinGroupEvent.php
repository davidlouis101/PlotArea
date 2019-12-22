<?php

namespace mohagames\PlotArea\events;

use mohagames\PlotArea\utils\Plot;
use pocketmine\event\Cancellable;

class PlotJoinGroupEvent extends PlotEvent implements Cancellable
{

    public function getPlot(): Plot
    {
        // TODO: Implement getPlot() method.
    }

}