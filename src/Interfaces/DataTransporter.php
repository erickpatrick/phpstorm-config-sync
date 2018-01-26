<?php
/**
 * Created by PhpStorm.
 * User: dealveri
 * Date: 25.01.2018
 * Time: 13:27
 */

namespace Nintendo\Translator\Interfaces;


interface DataTransporter
{
    public function getData();
    public function setData($data): DataTransporter;
}