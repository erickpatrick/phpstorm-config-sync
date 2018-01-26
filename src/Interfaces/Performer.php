<?php namespace Nintendo\Translator\Interfaces;

interface Performer
{
    public function setNext(DataTransformer $next): void;
    public function next(DataTransporter $data, $newData): DataTransporter;
}