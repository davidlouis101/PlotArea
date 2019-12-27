<?php

/*
  _____   _         _
 |  __ \ | |       | |      /\
 | |__) || |  ___  | |_    /  \    _ __  ___   __ _
 |  ___/ | | / _ \ | __|  / /\ \  | '__|/ _ \ / _` |
 | |     | || (_) || |_  / ____ \ | |  |  __/| (_| |
 |_|     |_| \___/  \__|/_/    \_\|_|   \___| \__,_|
 */

namespace mohagames\PlotArea\events\permission;

use mohagames\PlotArea\utils\Plot;

class MemberSetPermissionEvent extends PermissionEvent
{

    private $plot;
    private $member;
    private $permission;
    private $value;

    public function __construct(Plot $plot, string $member, string $permission, bool $value)
    {
        $this->plot = $plot;
        $this->member = $member;
        $this->permission = $permission;
        $this->value = $value;
    }

    public function getMember()
    {

        return $this->member;

    }

    public function getPlot(): Plot
    {
        return $this->plot;
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    public function getValue()
    {
        return $this->value;
    }


}