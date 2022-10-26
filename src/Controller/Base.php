<?php

namespace Nicmaxcarter\SimpleQueue\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;

abstract class Base
{
    protected $sep;

    public function __construct()
    {
        $this->sep = DIRECTORY_SEPARATOR;
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
}
