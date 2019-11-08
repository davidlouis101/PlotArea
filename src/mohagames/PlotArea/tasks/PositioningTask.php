<?php

namespace mohagames\PlotArea\tasks;


use mohagames\PlotArea\Main;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class PositioningTask extends Task{

    public $main;
    public $isInPlot = array();

    public function __construct(){
        $this->main = Main::getInstance();
    }

    public function onRun(int $currentTick){

        foreach($this->main->getServer()->getOnlinePlayers() as $player){
            $plot = $this->main->getPlot($player);

            if($plot !== null){
                if($plot->isGrouped()) {
                    $plot = $plot->getGroup()->getMasterPlot();
                }
                if(!isset($this->isInPlot[$player->getName()])){
                    $this->isInPlot[$player->getName()] = null;
                }
                $status = $this->isInPlot[$player->getName()];
                if($plot->getId() !== $status ){
                    $text = $plot->getOwner() ? $plot->getOwner() : "§aGeen eigenaar";
                    $player->sendPopup(TextFormat::RED . $plot->getName() . "\n" . TextFormat::BLUE . $text);
                    $this->isInPlot[$player->getName()] = $plot->getId();
                }
            }
            else{
                $this->isInPlot[$player->getName()] = null;
            }
        }
    }


}