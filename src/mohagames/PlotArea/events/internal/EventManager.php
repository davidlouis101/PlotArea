<?php


namespace mohagames\PlotArea\events\internal;



class EventManager{
    public static $handlers;
    public static $handlerList;
    public static $objList;
    private static $events = [
        "mohagames\PlotArea\\events\PlotRemoveMemberEvent",
        "mohagames\PlotArea\\events\PlotAddMemberEvent",
        "mohagames\PlotArea\\events\PlotSetOwnerEvent",
        "mohagames\PlotArea\\events\PlotEnterEvent",
        "mohagames\PlotArea\\events\PlotDeleteEvent"
    ];

    public static function registerListener(PlotListener $pl){

        $rc = new \ReflectionClass($pl);

        foreach($rc->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {

            if ($method->class == $rc->getName()) {
                $parameters = $method->getParameters();

                foreach ($parameters as $parameter) {
                    $type = $parameter->getType();
                    if ($type !== null) {
                        $name = $type->getName();

                        if (in_array($name, EventManager::$events)) {
                            $handlers[] = $method;
                        }
                    }
                }
            }
        }
        if(isset($handlers)){
            self::$handlerList[] = [$rc->getName() => $handlers];
            self::$objList[] = [$rc->getName() => $pl];
            $pl->getLogger()->alert("This plugin is now listening to the PlotArea plugin. Be aware that the Event caller functions are experimental!");
        }
        else{
            self::$handlerList[] = [];
            self::$objList[] = [];

        }
    }
}