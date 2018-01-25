<?php require __DIR__ . "/vendor/autoload.php";

use Nintendo\Translator\TranslationDataBuilder;
use Nintendo\Translator\TranslationDataFormatter;
use Nintendo\Translator\TranslationDataMerger;
use Nintendo\Translator\TranslationGenerator;
use Nintendo\Translator\TranslationToCsvFileCreator;

$dataBuilder = new TranslationDataBuilder('./8085-translations.xls');
$dataFormatter = new TranslationDataFormatter();
$dataMerger = new TranslationDataMerger();
$fileCreator = new TranslationToCsvFileCreator();
$translationGenerator = new TranslationGenerator($dataBuilder, $dataFormatter, $dataMerger, $fileCreator);

var_dump($translationGenerator->execute());