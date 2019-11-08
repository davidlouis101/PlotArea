<?php

namespace mohagames\PlotArea\utils;

use mohagames\LevelAPI\utils\LevelManager;
use mohagames\PlotArea\Main;
use pocketmine\event\server\LowMemoryEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;

class Plot extends PermissionManager {
    protected $name;
    protected $owner;
    protected $level;
    protected $location;
    protected $members;
    protected $plot_id;
    public $db;
    public $main;

    /*
     * TODO: $level moet geen Level class zijn maar een string van de levelnaam. Dit maakt het inladen van werelden vele gemakkelijker.
     */

    public function __construct($name, $owner, Level $level, array $location, array $members = array())
    {
        $this->name = $name;
        $this->level = $level;
        $this->owner = $owner;
        $this->location = new Location($location);
        $this->members = $members;
        $this->db = Main::getInstance()->db;
        $this->main = Main::getInstance();
        $this->plot_id = $this->getId();
        parent::__construct($this->getPlot());
    }


    public static function save($name, Level $level, array $location, $owner = null, array $members = array()){
        $db = Main::getInstance()->db;
        $members_ser = serialize($members);
        $location_ser = serialize($location);
        $max_members = Main::getInstance()->getConfig()->get("max_members");
        $levelname = $level->getName();
        $stmt = $db->prepare("INSERT INTO plots (plot_name, plot_owner, plot_members, plot_location, plot_world, max_members) values(:plot_name, :plot_owner, :plot_members, :plot_location, :plot_world, :max_members)");
        $stmt->bindParam("plot_name", $name, SQLITE3_TEXT);
        $stmt->bindParam("plot_owner", $owner, SQLITE3_TEXT);
        $stmt->bindParam("plot_members", $members_ser, SQLITE3_TEXT);
        $stmt->bindParam("plot_location", $location_ser, SQLITE3_TEXT);
        $stmt->bindParam("plot_world", $levelname, SQLITE3_TEXT);
        $stmt->bindParam("max_members", $max_members, SQLITE3_INTEGER);
        $stmt->execute();
        $stmt->close();

        return new Plot($name, $owner, $level, $location, $members);
    }

    public static function get(Position $position)
    {
        $main = Main::getInstance();
        $result = $main->db->query("SELECT * FROM plots");
        while ($row = $result->fetchArray()) {
            $plot_level = null;
            if($main->getServer()->isLevelGenerated($row["plot_world"])){
                if($main->getServer()->isLevelLoaded($row["plot_world"])){
                    $plot_level = $main->getServer()->getLevelByName($row["plot_world"]);
                }
                else{
                    if($main->getServer()->loadLevel($row["plot_world"])){
                        $plot_level = $main->getServer()->getLevelByName($row["plot_world"]);
                    }
                }
            }
            if($plot_level == null){
                return null;
            }
            $plot = new Plot($row["plot_name"], $row["plot_owner"], $plot_level, unserialize($row["plot_location"]), unserialize($row["plot_members"]));
            $location = $plot->getLocation();
            $location = $location->calculateCoords();
            $pos1 = $location->getPos1();
            $pos2 = $location->getPos2();
            $p_x = $position->getFloorX();
            $p_y = $position->getFloorY();
            $p_z = $position->getFloorZ();
            $level = $position->getLevel();
            $res = false;
            if ($pos1["y"] == $pos2["y"]) {
                $res = ($p_x <= $pos1["x"] && $p_x >= $pos2["x"] && $p_z <= $pos1["z"] && $p_z >= $pos2["z"]);
            }
            if (($p_x <= $pos1["x"] && $p_x >= $pos2["x"] && $p_z <= $pos1["z"] && $p_z >= $pos2["z"]) && (($p_y >= $pos1["y"] && $p_y < $pos2["y"]) || $res) && $plot->getLevel() === $level) {
                return $plot;
                break;
            }
        }
    }

    public function getPlot() : Plot{
        return $this;
    }

    public function getName(){
        $plot_id = $this->getId();
        if($this->isGrouped()){
            $name = $this->getGroup()->getName();
        }
        else{
            $stmt = $this->db->prepare("SELECT plot_name FROM plots WHERE plot_id = :plot_id");
            $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
            $res = $stmt->execute();
            while($row = $res->fetchArray()){
                $name = $row["plot_name"];
            }
            $stmt->close();
        }

        return $name;
    }

    public function getOwner(){
        $plot_id = $this->getId();
        if($this->isGrouped()){
            $plot_id = $this->getGroup()->getMasterPlot()->getId();
        }
        $stmt = $this->db->prepare("SELECT plot_owner FROM plots WHERE plot_id = :plot_id");
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            $owner = $row["plot_owner"];
        }
        $stmt->close();
        return $owner;
    }

    public function isOwner(string $player) : bool{
        $owner = $this->getOwner();
        if($this->isGrouped()){
            $owner = $this->getGroup()->getMasterPlot()->getOwner();
        }
        return strtolower($player) == $owner;
    }


    public function getLevel(){
        return $this->level;
    }


    public function getLevelName(){
        return $this->level->getName();
    }


    public function getLocation(){
        return $this->location;
    }


    public function getMembers(){
        $plot_id = $this->getId();
        if($this->isGrouped()){
            $plot_id = $this->getGroup()->getMasterPlot()->getId();
        }
        $stmt = $this->db->prepare("SELECT plot_members FROM plots WHERE plot_id = :plot_id");
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            $members = unserialize($row["plot_members"]);
        }
        $stmt->close();
        return $members;
    }

    public function isMember($member) : bool{
        $members = $this->getMembers();
        if($this->isGrouped()){
            $members = $this->getGroup()->getMasterPlot()->getMembers();
        }
        $member = strtolower($member);
        return in_array($member, $members);
    }

    public function getMembersList(){
        $members = $this->getMembers();
        if($this->isGrouped()){
            $members = $this->getGroup()->getMasterPlot()->getMembers();
        }
        if (count($members) == 0) {
            return false;
        } else {
            $leden = "";
            foreach ($members as $lid) {
                $leden .= "$lid, ";
            }
            $leden = rtrim($leden, ', ');
            return $leden;
        }
    }

    public function getSize() : array {
        $loc = $this->getLocation();
        $loc = $loc->calculateCoords();
        $pos1 = $loc->getPos1();
        $pos2 = $loc->getPos2();

        $x_size = $pos1["x"] - $pos2["x"] + 1;
        $z_size = $pos1["z"] - $pos2["z"] + 1;

        return array($x_size, $z_size);

    }

    public function getId() : int{
        $conn = $this->db->prepare("SELECT plot_id FROM plots WHERE plot_location = :location");
        $loc = serialize($this->getLocation()->getLocation());
        $conn->bindParam("location",$loc , SQLITE3_TEXT);
        $result = $conn->execute();
        while($row = $result->fetchArray()){
            return $row["plot_id"];
            break;
        }
        $conn->close();
    }

    public function setOwner($owner = null) : bool{
        $owner = $owner ? strtolower($owner) : null;
        $lvl = new LevelManager();
        if($lvl->userExists($owner) || is_null($owner)) {
            $plot_id = $this->getId();
            if ($this->isGrouped()) {
                $plot_id = $this->getGroup()->getMasterPlot()->getId();
            }
            $stmt = $this->db->prepare("UPDATE plots SET plot_owner = :plot_owner WHERE plot_id = :plot_id");
            $stmt->bindParam("plot_owner", $owner, SQLITE3_TEXT);
            $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
            return true;
        }
        else{
            return false;
        }
    }

    public function addMember($member) : bool{
        $member = strtolower($member);
        if(LevelManager::getManager()->userExists($member)) {
            $members = $this->getMembers();
            $plot_id = $this->getId();
            $plot = $this->getPlot();
            if ($this->isGrouped()) {
                $plot_id = $this->getGroup()->getMasterPlot()->getId();
                $members = $this->getGroup()->getMasterPlot()->getMembers();
                $plot = $this->getGroup()->getMasterPlot();
            }
            if (!empty($member)) {
                if (count($members) < $this->getMaxMembers() && !in_array($member, $members)) {
                    array_push($members, $member);
                    $members = serialize($members);
                    $stmt = $this->db->prepare("UPDATE plots SET plot_members = :plot_members WHERE plot_id = :plot_id");
                    $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
                    $stmt->bindParam("plot_members", $members, SQLITE3_TEXT);
                    $stmt->execute();
                    $stmt->close();
                    if ($plot->getPlayerPermissions($member) == null) {
                        $plot->initPlayerPerms($member);
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        else{
            return false;
        }
    }



    public function getMaxMembers() : int{
        $plot_id = $this->getId();
        $stmt = $this->db->prepare("SELECT max_members FROM plots WHERE plot_id = :plot_id");
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            return $row["max_members"];
        }
    }

    public function setMaxMembers(int $max_count){
        $plot_id = $this->getId();
        $stmt = $this->db->prepare("UPDATE plots SET max_members = :max_members WHERE plot_id = :plot_id");
        $stmt->bindParam("max_members", $max_count, SQLITE3_INTEGER);
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $stmt->execute();
        $stmt->close();
    }

    public function removeMember($member) : bool{
        $member = strtolower($member);
            $old_members = $this->getMembers();
            $plot_id = $this->getId();

            if ($this->isGrouped()) {
                $plot_id = $this->getGroup()->getMasterPlot()->getId();
                $old_members = $this->getGroup()->getMasterPlot()->getMembers();
            }
            if (in_array($member, $old_members)) {
                $members = serialize(array_diff($old_members, array($member)));
                $stmt = $this->db->prepare("UPDATE plots SET plot_members = :plot_members WHERE plot_id = :plot_id");
                $stmt->bindParam("plot_members", $members, SQLITE3_TEXT);
                $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
                $stmt->execute();
                $stmt->close();
                $this->destructPlayerPerms($member);
                return true;
            } else {
                return false;
            }
    }

    public function setGroupName(?string $name) : void{
        $plot_id = $this->getId();
        $stmt = $this->db->prepare("UPDATE plots SET group_name = :group_name WHERE plot_id = :plot_id");
        $stmt->bindParam("group_name", $name, SQLITE3_TEXT);
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $stmt->execute();
        $stmt->close();
    }

    public function getGroupName(){
        $plot_id = $this->getId();
        $stmt = $this->db->prepare("SELECT group_name FROM plots WHERE plot_id = :plot_id");
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            $group_name = $row["group_name"];
        }
        $stmt->close();

        if(isset($group_name) && !is_null($group_name)){
            return $group_name;
        }
        else{
            return false;
        }
    }

    public function getGroup() : ?Group{
        $group_name = $this->getGroupName();
        $db = $this->db;
        $stmt = $db->prepare("SELECT * FROM groups WHERE group_name = :group_name");
        $stmt->bindParam("group_name", $group_name, SQLITE3_TEXT);
        $res = $stmt->execute();
        $group = null;
        while($row = $res->fetchArray()){
            $group =  new Group($row["group_name"], Main::getInstance()->getPlotByName($row["master_plot"]));
        }
        return $group;
    }


    public function isGrouped(){
        $group = $this->getGroup();

        if($group !== null){
            return true;
        }
        else{
            return false;
        }
    }

    public function isMasterPlot() : ?bool{
        if($this->isGrouped()){
            $exp = $this->getGroup()->getMasterPlot()->getName() == $this->getName();
        }
        else{
            return null;
        }
        return $exp;
    }

    public static function getPlots() : array{
        $res = Main::getInstance()->db->query("SELECT * FROM plots");
        $plots = null;
        while($row = $res->fetchArray()){
            $plots[] = Main::getInstance()->getPlotById($row["plot_id"]);
        }
        return $plots;
    }

    public static function getPlotById(int $id) : ?Plot
    {
        $main = Main::getInstance();
        $stmt = $main->db->prepare("SELECT * FROM plots WHERE plot_id = :id");
        $stmt->bindParam("id", $id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        $plot = null;
        while ($row = $res->fetchArray()) {
            if($main->getServer()->isLevelLoaded($row["plot_world"])){
                $world = $main->getServer()->getLevelByName($row["plot_world"]);
            }
            elseif($main->getServer()->isLevelGenerated($row["plot_world"])){
                if($main->getServer()->loadLevel($row["plot_world"])){
                    $world = $main->getServer()->getLevelByName($row["plot_world"]);
                }
            }
            if($world == null){
                return null;
            }
            $plot = new Plot($row["plot_name"], $row["plot_owner"], $world, unserialize($row["plot_location"]), unserialize($row["plot_members"]));
        }
        return $plot;
    }

    public static function getPlotByName(string $name): ?Plot
    {
        $main = Main::getInstance();
        $name = strtolower($name);
        $stmt = $main->db->prepare("SELECT * FROM plots WHERE lower(plot_name) = :plot_name");
        $stmt->bindParam("plot_name", $name, SQLITE3_TEXT);
        $res = $stmt->execute();
        while ($row = $res->fetchArray()) {
            if($main->getServer()->isLevelLoaded($row["plot_world"])){
                $world = $main->getServer()->getLevelByName($row["plot_world"]);
            }
            elseif($main->getServer()->isLevelGenerated($row["plot_world"])){
                if($main->getServer()->loadLevel($row["plot_world"])){
                    $world = $main->getServer()->getLevelByName($row["plot_world"]);
                }
            }
            if($world == null){
                return null;
            }

            $plot = new Plot($row["plot_name"], $row["plot_owner"], $world, unserialize($row["plot_location"]), unserialize($row["plot_members"]));
        }
        if (isset($plot)) {
            return $plot;
        } else {
            return null;
        }

    }



    public function delete(){
        if($this->isGrouped()){
            if($this->isMasterPlot()){
                $this->getGroup()->delete();
            }
            else{
                $this->getGroup()->removeFromGroup($this);
            }
        }
        PublicChest::deleteChests($this);
        $plot_id = $this->getId();
        $stmt = $this->db->prepare("DELETE FROM plots WHERE plot_id = :plot_id");
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        $stmt->close();

        return $res;
    }



    public function reset(){
        $plot_id = $this->getId();
        if($this->isGrouped()){
            $plot_id = $this->getGroup()->getMasterPlot()->getId();
        }
        $this->setOwner();
        $stmt = $this->db->prepare("UPDATE plots SET plot_members = :plot_members WHERE plot_id = :plot_id");
        $empty_array = serialize(array());
        $stmt->bindParam("plot_members", $empty_array, SQLITE3_TEXT);
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $stmt->execute();
        return true;
    }
}