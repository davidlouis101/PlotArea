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

class GroupSetNameEvent extends GroupEvent
{

    private $group;
    private $oldname;
    private $newname;

    public function __construct(Group $group, string $oldname, string $newname)
    {
        $this->group = $group;
        $this->oldname = $oldname;
        $this->newname = $newname;
    }

    public function getOldName(): string
    {
        return $this->oldname;
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