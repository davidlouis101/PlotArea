<?php


namespace mohagames\PlotArea\utils;


use mohagames\PlotArea\Main;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class PublicChest
{

    public $chest_location;

    public function __construct(array $chest_location, string $chest_world, int $plot_id)
    {
        $this->chest_location = $chest_location;

    }

    public static function getChest(Position $location) : ?PublicChest{
        $world = $location->getLevel()->getName();
        $location = serialize([$location->getFloorX(), $location->getFloorY(), $location->getFloorZ()]);
        $stmt = Main::getInstance()->db->prepare("SELECT * FROM chests WHERE chest_location = :chest_location AND chest_world = :chest_world");
        $stmt->bindParam("chest_location", $location, SQLITE3_TEXT);
        $stmt->bindParam("chest_world", $world, SQLITE3_TEXT);
        $res = $stmt->execute();
        $chest = null;
        while($row = $res->fetchArray()){
            $chest = new PublicChest(unserialize($row["chest_location"]), $row["chest_world"], $row["plot_id"]);
        }
        $stmt->close();
        return $chest;
    }

    /*
     * DEPRECATED niet gebruiken want deze functie returned ALTIJD true omdat ik dom ben.
     */
    public function isPublic() : bool{
        $loc = $this->getLocation();
        $level = $this->getLevel();
        $vector = new Position($loc[0], $loc[1], $loc[2], $level);
        if(PublicChest::getChest($vector) !== null){
            return true;
        }
        else{
            return false;
        }
    }

    public function getPlot() : Plot{
        return Main::getInstance()->getPlotById($this->getPlotId());
    }

    public function getPlotId() : int{
        $chest_id = $this->getId();
        $stmt = Main::getInstance()->db->prepare("SELECT plot_id FROM chests WHERE chest_id = :chest_id");
        $stmt->bindParam("chest_id", $chest_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            $plot_id = $row["plot_id"];
        }
        $stmt->close();
        return $plot_id;
    }

    public static function getChests() : ?array {
        $res = Main::getInstance()->db->query("SELECT * FROM chests");
        $chest = null;
        while($row = $res->fetchArray()){
            $chests[] = new PublicChest(unserialize($row["chest_location"]), $row["chest_world"], $row["plot_id"]);
        }
        return $chest;
    }

    public static function deleteChests(Plot $plot){
        $plot_id = $plot->getId();
        $stmt = Main::getInstance()->db->prepare("DELETE FROM chests WHERE plot_id = :plot_id");
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function getId() : int{
        $location = serialize($this->getLocation());
        $stmt = Main::getInstance()->db->prepare("SELECT chest_id FROM chests WHERE chest_location = :chest_location");
        $stmt->bindParam("chest_location", $location, SQLITE3_TEXT);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            $chest_id = $row["chest_id"];
        }
        $stmt->close();
        return $chest_id;
    }

    public function getLocation(){
        return $this->chest_location;
    }

    public function getLevel(){
        $chest_id = $this->getId();
        $stmt = Main::getInstance()->db->prepare("SELECT chest_world WHERE chest_id = :chest_id");
        $stmt->bindParam("chest_id", $chest_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            $world_name = $row["chest_world"];
        }
        $stmt->close();
        $level = Main::getInstance()->getServer()->getLevelByName($world_name);
        return $level;

    }

    public static function save(Vector3 $chest_location, Level $chest_world, Plot $plot){
        $chest_location = serialize([$chest_location->getFloorX(), $chest_location->getFloorY(), $chest_location->getFloorZ()]);
        $chest_world_name = $chest_world->getName();
        $plot_id = $plot->getId();
        $stmt = Main::getInstance()->db->prepare("INSERT INTO chests (chest_location, chest_world, plot_id) values(:chest_location,:chest_world, :plot_id)");
        $stmt->bindParam("chest_location", $chest_location, SQLITE3_TEXT);
        $stmt->bindParam("chest_world", $chest_world_name, SQLITE3_TEXT);
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function delete(){
        $chest_id = $this->getId();
        $stmt = Main::getInstance()->db->prepare("DELETE FROM chests WHERE chest_id = :chest_id ");
        $stmt->bindparam("chest_id", $chest_id, SQLITE3_INTEGER);
        $stmt->execute();
    }

}