<?php


/*
  _____   _         _
 |  __ \ | |       | |      /\
 | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 */


namespace mohagames\PlotArea\events\group;

use mohagames\PlotArea\utils\Group;
use pocketmine\Player;

class GroupSetNameEvent extends GroupEvent
{

    private $group;
    private $oldname;
    private $newname;
    private $player;

    public function __construct(Group $group, string $oldname, string $newname, ?Player $executor = null)
    {
        $this->group = $group;
        $this->oldname = $oldname;
        $this->newname = $newname;
        $this->player = $executor;
    }

    public function getOldName(): string
    {
        return $this->oldname;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function getNewName(): string
    {
        return $this->newname;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

}