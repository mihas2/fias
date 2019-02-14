<?php
namespace Fias\Api;

abstract class ApiAbstract
{
    /**
     * @access private
     * @param mixed $data
     * @param array $params
     * @return ResponseValue
     */
    protected function response($data, array $params = [])
    {
        return new ResponseValue($data, $params);
    }
}
