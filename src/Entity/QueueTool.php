<?php

namespace Nicmaxcarter\SimpleQueue\Entity;

class QueueTool
{
    public static function getIp()
    {
        //$dig = "dig +short myip.opendns.com @resolver1.opendns.com";
        $dig = "wget -qO- https://ifconfig.me ; echo";
        $myIp = trim(shell_exec($dig));

        return $myIp;
    }
}


