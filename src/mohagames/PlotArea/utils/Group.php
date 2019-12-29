<?php

/*
  _____   _         _
 |  __ \ | |       | |      /\
 | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 */

namespace mohagames\PlotArea\utils;


use mohagames\PlotArea\events\group\GroupDeleteEvent;
use mohagames\PlotArea\events\group\GroupSetMasterPlotEvent;
use mohagames\PlotArea\events\group\GroupSetNameEvent;
use mohagames\PlotArea\Main;
use pocketmine\Player;


/**
 * Class Group
 *
 * A group is a group of multiple Plots, the group consists of a Master Plot and Member Plots.
 * All the changes that are applied to the Master Plot will have an effect on the whole Group.
 *
 * @package mohagames\PlotArea\utils
 */
class Group
{

    protected $group_name;
    protected $db;

    public function __construct($group_name, Plot $master_plot)
    {
        $this->db = Main::GetInstance()->db;
        $this->group_name = $group_name;
    }

    public static function getGroup($group_name) : ?Group{
        $group_name = strtolower($group_name);
        $db = Main::getInstance()->db;
        $stmt = $db->prepare("SELECT * FROM groups WHERE lower(group_name) = :group_name");
        $stmt->bindParam("group_name", $group_name, SQLITE3_TEXT);
        $res = $stmt->execute();
        $group = null;
        while($row = $res->fetchArray()){
            $group = new Group($row["group_name"], Plot::getPlotByName($row["master_plot"]));
        }
        return $group;
    }

    public static function saveGroup($group_name, Plot $master_plot, Plot $second_plot){
        $db = Main::getInstance()->db;

        if($master_plot !== null && $second_plot !== null){
            if($master_plot->getName() != $second_plot->getName()){
                $master_plot->setGroupName($group_name);
                $second_plot->setGroupName($group_name);
                $master_plot = $master_plot->getName();
                $stmt = $db->prepare("INSERT INTO groups (group_name, master_plot) values(:group_name, :master_plot)");
                $stmt->bindParam("group_name", $group_name, SQLITE3_TEXT);
                $stmt->bindParam("master_plot", $master_plot, SQLITE3_TEXT);
                $stmt->execute();
                return true;
            } else {
                return false;
            }
        }
    }

    public function getGroupId() : int{
        $stmt = $this->db->prepare("SELECT group_id FROM groups WHERE group_name = :group_name");
        $stmt->bindParam("group_name", $this->group_name, SQLITE3_TEXT);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            return $row["group_id"];
        }
    }

    public function getName(){
        $group_id = $this->getGroupId();
        $stmt = $this->db->prepare("SELECT group_name FROM groups WHERE group_id = :group_id");
        $stmt->bindParam("group_id", $group_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            $name = $row["group_name"];
        }

        return $name;
    }

    public function getMasterPlot() : Plot{
        $group_id = $this->getGroupId();
        $stmt = $this->db->prepare("SELECT master_plot FROM groups WHERE group_id = :group_id");
        $stmt->bindParam("group_id", $group_id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        while($row = $res->fetchArray()){
            $master_plot = Plot::getPlotByName($row["master_plot"]);
        }

        return $master_plot;
    }

    /**
     * @return Plot[]
     */
    public function getPlots() : ?array {
        $group_name = $this->getName();
        $stmt = $this->db->prepare("SELECT plot_id FROM plots WHERE group_name = :group_name");
        $stmt->bindParam("group_name", $group_name, SQLITE3_TEXT);
        $res = $stmt->execute();
        $plots = null;
        while ($row = $res->fetchArray()) {
            $id = $row["plot_id"];
            $plots[] = Plot::getPlotById($id);
        }
        return $plots;
    }

    public function setName(string $name, Player $executor = null)
    {
        $ev = new GroupSetNameEvent($this, $this->getName(), $name, $executor);
        $ev->call();
        if (!$ev->isCancelled()) {
            $group_id = $this->getGroupId();
            $stmt = $this->db->prepare("UPDATE groups SET group_name = :group_name WHERE group_id = :group_id");
            $stmt->bindParam("group_name", $name, SQLITE3_TEXT);
            $stmt->bindParam("group_id", $group_id, SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function setMasterPlot(Plot $plot, Player $executor = null)
    {
        $ev = new GroupSetMasterPlotEvent($this, $plot, $executor);
        $ev->call();
        if (!$ev->isCancelled()) {
            $master_plot = $plot->getName();
            $group_id = $this->getGroupId();
            $stmt = $this->db->prepare("UPDATE groups SET master_plot = :group_name WHERE group_id = :group_id");
            $stmt->bindParam("master_plot", $master_plot, SQLITE3_TEXT);
            $stmt->bindParam("group_id", $group_id, SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function addToGroup(Plot $plot){
        if($plot->getGroupName() !== $this->getName()){
            $plot->setGroupName($this->getName());
        }
    }

    public function removeFromGroup(Plot $plot){
        $plot->setGroupName(null);
    }

    public function getGroupMembers(){
        $group_name = $this->getName();
        $stmt = $this->db->prepare("SELECT plot_name FROM plots WHERE group_name = :group_name");
        $stmt->bindParam("group_name", $group_name, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public static function groupExists($group_name){
        $group_name = strtolower($group_name);
        $stmt = Main::getInstance()->db->prepare("SELECT * FROM groups WHERE lower(group_name) = :group_name");
        $stmt->bindParam("group_name", $group_name, SQLITE3_TEXT);
        $res = $stmt->execute();

        $count = 0;
        while ($row = $res->fetchArray()) {
            $count++;
        }

        return $count > 0;
    }

    public function delete(Player $executor = null)
    {
        $ev = new GroupDeleteEvent($this, $executor);
        $ev->call();
        if (!$ev->isCancelled()) {
            $groupname = $this->getName();
            $stmt = $this->db->prepare("UPDATE plots SET group_name = NULL WHERE group_name = :group_name");
            $stmt->bindParam("group_name", $groupname, SQLITE3_TEXT);
            $stmt->execute();
            $stmt->close();
            $stmt = $this->db->prepare("DELETE FROM groups WHERE group_name = :group_name");
            $stmt->bindParam("group_name", $groupname, SQLITE3_TEXT);
            $stmt->execute();
            $stmt->close();
        }
    }

}