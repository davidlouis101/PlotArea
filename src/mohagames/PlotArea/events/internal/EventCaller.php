<?php


namespace mohagames\PlotArea\events\internal;


use mohagames\PlotArea\events\PlotEvent;
use mohagames\PlotArea\events\PlotRemoveMemberEvent;
use pocketmine\command\defaults\ReloadCommand;

class EventCaller
{


    public function __construct(PlotEvent $event)
    {

        $event_ref = new \ReflectionClass($event);
        $namespace = $event_ref->getName();
        $ObjHandlerList = EventManager::$handlerList;
        $idList = EventManager::$objList;



        /*
         * TODO: Efficienter maken lololo
         */

        foreach($idList as $obj_array){
            foreach($obj_array as $name => $obj){

                foreach($ObjHandlerList as $handlersArray){

                    foreach($handlersArray as $handler_name => $handlers){

                        if($handler_name == $name){

                            foreach($handlers as $handler){

                                foreach($handler->getParameters() as $parameter){

                                    if($parameter->getType()->getName() == $namespace){

                                        $handler->invokeArgs($obj, [$event]);

                                    }

                                }
                            }
                        }
                    }
                }
            }
        }



    }

}