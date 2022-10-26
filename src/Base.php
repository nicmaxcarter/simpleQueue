<?php

namespace Nicmaxcarter\SimpleQueue;

use Doctrine\DBAL\Connection as DB;

class Base
{
    protected $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    // I'm tired of typing this shit out when debugging
    // dumps data, and halts the program afterwards
    public function dump($data)
    {
        $this->dumpon($data);
        echo '_';
        exit;
    }

    // dumps data, helpful for debugging
    public function dumpon($data)
    {
        echo '<pre>';
        var_dump($data);
    }

    protected function query()
    {
        return $this->db->createQueryBuilder();
    }
}
