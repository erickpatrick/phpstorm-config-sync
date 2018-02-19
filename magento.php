<?php require __DIR__ . "/vendor/autoload.php";

use Nintendo\Translator\Data;
use Nintendo\Translator\Magento\Builder;
use Nintendo\Translator\Magento\Creator;
use Nintendo\Translator\Magento\Formatter;
use Nintendo\Translator\Magento\Merger;

// instantiate necessary transformers and data
$dataTransporter = new Data('./8085-translations.xls');
$builder = new Builder();
$formatter = new Formatter();
$merger = new Merger();
$creator = new Creator();

// set chain of responsibility
$builder->setNext($formatter);
$formatter->setNext($merger);
$merger->setNext($creator);

// execute
var_dump($builder->execute($dataTransporter));
