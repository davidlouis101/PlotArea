<?php

namespace mohagames\PlotArea\events\group;


use mohagames\PlotArea\utils\Group;
use mohagames\PlotArea\utils\Plot;

class GroupSetMasterPlotEvent extends GroupEvent
{


    private $group;
    private $masterplot;

    public function __construct(Group $group, Plot $masterplot)
    {
        $this->group = $group;
        $this->masterplot = $masterplot;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getPlot(): Plot
    {
        return $this->masterplot;
    }

}