<?php

namespace Yunshop\Love\Common\Modules;

class Repository
{
    private $data;

    public function __construct($data, $key)
    {
        $result = [];
        foreach ($data as $item) {
            $result[$item[$key]] = $item;
        }
        $this->data = $result;
    }

    public function find($id)
    {
        return $this->data[$id];
    }

    public function all()
    {
        return $this->data;
    }
}