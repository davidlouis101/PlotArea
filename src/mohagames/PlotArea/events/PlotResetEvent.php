<?php

namespace mohagames\PlotArea\events;

use mohagames\PlotArea\utils\Plot;

class PlotResetEvent extends PlotEvent
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