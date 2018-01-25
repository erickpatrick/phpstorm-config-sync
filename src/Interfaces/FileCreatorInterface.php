<?php namespace Nintendo\Translator\Interfaces;

interface FileCreatorInterface
{
    public function execute(array $content): array;
}