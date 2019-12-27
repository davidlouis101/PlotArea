<?php

namespace mohagames\PlotArea\utils;

class Member
{


    public static function exists(string $playername)
    {
        $playername = strtolower($playername);
        return file_exists("players/$playername.dat");

    }


}