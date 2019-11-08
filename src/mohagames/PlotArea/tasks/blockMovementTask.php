<?php

namespace mohagames\PlotArea\tasks;


use mohagames\PlotArea\listener\EventListener;
use mohagames\PlotArea\Main;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class blockMovementTask extends Task{


    public $player;
    public $event;

    public function __construct(EventListener $event, Player $player){
        $this->event = $event;
        $this->player = $player;
    }

    public function onRun(int $currentTick){
        unset($this->event->cancelMovement[$this->player->getName()]);
    }


}