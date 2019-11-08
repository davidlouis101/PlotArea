# PlotArea changelog

##1.0.0

* Added basic Plot functionality
* Per plot permissions added
* Plot wand added for more precise and easy plot making

## 1.1.0
* ~~Plot collision detection~~ (broken)

## 2.0.0
* Created a Plot class:
    * `$Plot->getOwner()`
    * `$plot->getName()`
    * `$plot->getLevel()` => returns an instance of `Level`
    * `$plot->getLevelName()` => returns the name of the Level
    * `$plot->getLocation()` => returns an instance of `Location`
    * `$plot->getMembers()` => returns an array with all the members of the Plot
    * `$plot->isMember(string $member)` => checks if the given user is a member of the plot, returns a boolean.
    * `$plot->getSize()` => returns the size of the plot
    * `$plot->getId()` => returns the Id of the plot
    * `$plot->setOwner(string $owner)` => sets the owner of the plot if `None` is given the Owner will be set to `NULL` which indicates that there is no Owner
    * `$plot->addMember(string $member)` => adds a member to the plot, returns true if successful and false if it failed
    * `$plot->removeMember(string $member)` => removes a member from the plot, returns true if successful and false if it failed
    * `$plot->delete()` => deletes the plot, returns a boolean
    
* Created a `Location` class (deprecated):
    * 
     
* Changed datatype to SQLite

## 2.1.0
* Renamed plottool to plotwand
* You can now edit the ploteditor item in the config

## 2.2.0
* When you go on a Plot you get a title popup with the name of the plot and the name of the owner.

## 3.0.0
* Created a small economy system:
    * `$plot->getPrice()` => returns an array of `Item` objects
    * `$plot->setPrice(array $price)` sets the price of a plot
    * `$plot->canBuy(Player $user)` returns a Boolean based on whether the user can buy the plot or not
    * `$plot->buy(Player $buyer)` buys the plot
    * `$plot->sell(Player $seller)` sells the plot (WIP)

## 3.1.0
* Added a new method `$plot->getArrayPrice()`, this returns an array in the following format. `array("item_id" => $item_id, "count" => $count)`
* Added the /setprice <itemid:count\> command

## 3.2.0

* Improved the economy system (WIP)

## 3.3.0 
* Removed all the economy features

## 4.0.0
* Plot detection will also depend on the y-coord
* Plots now can be linked together to create one giant plot
* Added a permission system

## 4.1.0
* PublicChests toegevoegd, vanaf nu kan een plot eigenaar een kist openbaar maken. Dit wil zeggen dat iedereen de kist kan openen
* Een paar internal changes, de manier waarop een plot wordt verwijderd is veranderd
* Grouping methods gemoved naar een andere class (gisteren gedaan)
* Command info lijst is nu deels gebaseerd op welke permissions je hebt
* Wanneer je een lid toevoegt aan een plot dan krijgt dat lid ook de default plot permissions.
* Command permissions gefixt
* /plotinfo geeft nu informatie gebaseerd op jouw rang
* Er wordt `x`xp afgenomen als je bent verwijderd als eigenaar van een plot
* PlayerPermissions worden allemaal ingesteld naar False wanneer een speler geen lid meer is van een plot

## 4.2.0
* Moved Plot related methods to the plot Class, you can now use Plot::get(), Plot::getPlotByName(), Plot::getPlotById() without having to make an instance of the Plot class.

## 4.3.0
* Fixed command permissions

## 4.4.0
* Fixed some issues

## NEXT 4.5.0
* Remove the LevelAPI dependency and move everything to an external plugin

## NEXT 5.0.0
* Create an event caller

