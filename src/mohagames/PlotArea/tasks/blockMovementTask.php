<?php

/*
  _____   _         _
 |  __ \ | |       | |      /\
 | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 */

namespace mohagames\PlotArea\tasks;


use mohagames\PlotArea\listener\EventListener;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class blockMovementTask extends Task
{


    public $player;
    public $event;

    public function __construct(EventListener $event, Player $player)
    {
        $this->event = $event;
        $this->player = $player;
    }

    public function onRun(int $currentTick)
    {
        unset($this->event->cancelMovement[$this->player->getName()]);
    }


}