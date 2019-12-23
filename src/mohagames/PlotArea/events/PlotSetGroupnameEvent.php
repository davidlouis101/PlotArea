<?php

namespace mohagames\PlotArea\events;

use mohagames\PlotArea\utils\Plot;

class PlotSetGroupnameEvent extends PlotEvent
{

    private $plot;
    private $grouname;

    public function __construct(Plot $plot, string $grouname)
    {
        $this->grouname = $grouname;
        $this->plot = $plot;
    }

    public function getPlot(): Plot
    {
        return $this->plot;
    }

    public function getGroupName(): string
    {
        return $this->grouname;
    }


}