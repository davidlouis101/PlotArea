<?php

/**
 * _____   _         _
 * |  __ \ | |       | |      /\
 * | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 * |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 * | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 * |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 * @author Mohamed El Yousfi
 */

namespace mohagames\PlotArea\utils;

use mohagames\PlotArea\Main;

class PermissionManager
{

    private $plot;
    private $db;

    public const PLOT_INTERACT_DOORS = "plot.interact.doors";
    public const PLOT_INTERACT_CHESTS = "plot.interact.chests";
    public const PLOT_INTERACT_TRAPDOORS = "plot.interact.trapdoors";
    public const PLOT_INTERACT_GATES = "plot.interact.gates";
    public const PLOT_INTERACT_ITEMFRAMES = "plot.interact.itemframes";
    public const PLOT_INTERACT_ARMORSTANDS = "plot.interact.armorstands";
    public const PLOT_SET_PINCONSOLE = "plot.set.pinconsole";
    public $permission_list;

    /**
     * De permission Manager zorgt ervoor dat alleen de bevoegde leden bepaalde acties kunnen uitvoeren op het Plot
     * De Plot class extend naar de PermissionManager class, dus is het niet nodig om een nieuwe instance aan te maken van de PermissionManager class.
     *
     * PermissionManager constructor.
     * @param Plot $plot
     */
    public function __construct(Plot $plot)
    {
        $this->plot = $plot;
        $this->db = Main::getInstance()->db;
        $this->permission_list = [
            self::PLOT_INTERACT_TRAPDOORS => true,
            self::PLOT_INTERACT_GATES => true,
            self::PLOT_INTERACT_CHESTS => true,
            self::PLOT_INTERACT_DOORS => true,
            self::PLOT_INTERACT_ITEMFRAMES => true,
            self::PLOT_INTERACT_ARMORSTANDS => true,
            self::PLOT_SET_PINCONSOLE => true
        ];
    }

    /**
     * Dit stelt de permission in van de gegeven speler.
     *
     * @param string $player
     * @param string $permission
     * @param bool $boolean
     * @return bool
     */
    public function setPermission(string $player, string $permission, bool $boolean)
    {
        if ($this->plot->isMember($player)) {
            if ($this->exists($permission)) {
                $player = strtolower($player);
                $player_perm = $this->getPermissions();
                $plot_id = $this->plot->getId();
                if ($player_perm !== null) {
                    if (!isset($player_perm[$player])) {
                        $this->initPlayerPerms($player);
                    }
                    $player_perm = $this->getPermissions();
                    $player_perm[$player][$permission] = $boolean;
                    $player_perm = serialize($player_perm);
                    $stmt = $this->db->prepare("UPDATE plots SET plot_permissions = :plot_permissions WHERE plot_id = :plot_id");
                    $stmt->bindParam("plot_permissions", $player_perm, SQLITE3_TEXT);
                    $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
                    $stmt->execute();
                    return true;
                } else {
                    $permissions = $this->permission_list;
                    $permissions[$permission] = $boolean;
                    $perms = serialize([$player => $permissions]);
                    $stmt = $this->db->prepare("UPDATE plots SET plot_permissions = :plot_permissions WHERE plot_id = plot_id");
                    $stmt->bindParam("plot_permissions", $perms, SQLITE3_TEXT);
                    $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
                    $stmt->execute();
                    return true;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Deze method initialiseert de permissions van de speler
     *
     * @param string $player
     */
    protected function initPlayerPerms(string $player)
    {
        $player = strtolower($player);
        if ($this->plot->isMember($player)) {
            $plot_id = $this->plot->getId();
            $perms = $this->getPermissions();

            $perms[$player] = $this->permission_list;

            $perms = serialize($perms);

            $stmt = $this->db->prepare("UPDATE plots SET plot_permissions = :plot_permissions WHERE plot_id = :plot_id");
            $stmt->bindParam("plot_permissions", $perms, SQLITE3_TEXT);
            $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
            $stmt->execute();
        }
    }

    /*
     * TODO: PlayerPerms moeten helemaal gedelete worden!
     */
    protected function destructPlayerPerms(string $player)
    {
        $permission_keys = array_keys($this->permission_list);
        if ($this->getPlayerPermissions($player) !== null) {
            foreach ($permission_keys as $perm) {
                $this->setPermission($player, $perm, false);
            }
        }
    }

    /**
     * Deze method returned alle permissions van de gegeven speler
     *
     * @param string $player
     * @return array|null
     */
    public function getPlayerPermissions(string $player): ?array
    {
        $permissions = $this->getPermissions();
        $player = strtolower($player);
        if (isset($permissions[$player])) {
            return $permissions[$player];
        } else {
            return null;
        }


    }

    /**
     * Deze method returned een array van alle permissions
     *
     * @return array|null
     */
    public function getPermissions(): ?array
    {
        $plot_id = $this->plot->getId();
        $stmt = $this->db->prepare("SELECT plot_permissions FROM plots WHERE plot_id = :plot_id");
        $stmt->bindParam("plot_id", $plot_id, SQLITE3_INTEGER);
        $res = $stmt->execute();

        while ($row = $res->fetchArray()) {
            $permissions = $row["plot_permissions"];
        }

        if ($permissions === null) {
            return $permissions;
        } else {
            return unserialize($permissions);
        }

    }

    /**
     * Deze method checkt als de gegeven speler de gegeven permission heeft
     *
     * @param string $player
     * @param string $permission
     * @return bool|null
     */
    public function hasPermission(string $player, string $permission): ?bool
    {
        if ($this->plot->isOwner($player)) {
            return true;
        } elseif ($this->plot->isMember($player)) {
            if ($this->exists($permission)) {
                $player = strtolower($player);
                $player_perms = $this->getPlayerPermissions($player);
                return $player_perms[$permission];
            } else {
                return null;
            }
        }else{
            return false;
        }

    }

    public static function resetAllPlotPermissions(){
        Main::getInstance()->db->query("UPDATE plots SET plot_permissions = NULL");
        $plots = Plot::getPlots();
        if($plots !== null){
            foreach($plots as $plot){
                    $members = $plot->getMembers();
                    foreach($members as $member){
                        $plot->initPlayerPerms($member);
                        Main::getInstance()->getLogger()->info("Member permissions succesvol ingesteld.");
                    }
                }
        }
    }

    public function exists(string $permission){
        $permission_keys = array_keys($this->permission_list);
        return in_array($permission, $permission_keys);

    }


}