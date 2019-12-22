<?php

namespace mohagames\PlotArea\events;

use mohagames\PlotArea\utils\Plot;
use pocketmine\event\Cancellable;

class PlotResetEvent extends PlotEvent implements Cancellable
{

    private $plot;

    public function __construct(Plot $plot)
    {
        $this->plot = $plot;
    }

    public function getPlot(): Plot
    {
        return $this->plot;
    }


}