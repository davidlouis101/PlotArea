<?php

/*
  _____   _         _
 |  __ \ | |       | |      /\
 | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 */

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