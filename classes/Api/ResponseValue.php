<?php
namespace Fias\Api;

use \Luracast\Restler\Data\ValueObject;

class ResponseValue extends ValueObject
{
    protected $data;
    protected $params;

    /**
     * @param string $data
     * @param array $params
     */
    public function __construct($data = null, array $params = [])
    {
        $this->data = $data;
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return $this->jsonSerialize();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'data' => $this->getData(),
            'params' => $this->getParams()
        ];
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
