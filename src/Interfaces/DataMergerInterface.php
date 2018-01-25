<?php namespace Nintendo\Translator\Interfaces;

interface DataMergerInterface
{
    public function execute(array $formattedTranslations): array;
}