<?php

namespace Fias\Api\v1;

use Fias\Api\ApiAbstract;

class Fias extends ApiAbstract
{
    /**
     * @return string
     */
    public function getIndex(){
        return "hello world";
    }
}
