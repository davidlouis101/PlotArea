<?php

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