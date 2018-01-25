<?php namespace Nintendo\Translator\Interfaces;

interface DataFormatterInterface
{
    public function execute(array $translations): array;
}