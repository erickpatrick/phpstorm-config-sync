<?php namespace Nintendo\Translator;

use Nintendo\Translator\Interfaces\DataTransporter;

class Data implements DataTransporter
{
    private $data;

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): DataTransporter
    {
        $this->data = $data;

        return $this;
    }
}