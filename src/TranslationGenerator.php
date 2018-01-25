<?php namespace Nintendo\Translator;

use Nintendo\Translator\Interfaces\DataBuilderInterface;
use Nintendo\Translator\Interfaces\DataFormatterInterface;
use Nintendo\Translator\Interfaces\DataMergerInterface;
use Nintendo\Translator\Interfaces\FileCreatorInterface;
use Nintendo\Translator\Interfaces\GeneratorInterface;

class TranslationGenerator implements GeneratorInterface
{
    /**
     * @var TranslationDataBuilder
     */
    private $dataBuilder;

    /**
     * @var TranslationDataFormatter
     */
    private $dataFormatter;

    /**
     * @var TranslationToCsvFileCreator
     */
    private $fileCreator;
    /**
     * @var DataMergerInterface
     */
    private $dataMerger;

    /**
     * TranslationGenerator constructor.
     *
     * @param DataBuilderInterface $dataBuilder
     * @param DataFormatterInterface $dataFormatter
     * @param DataMergerInterface $dataMerger
     * @param FileCreatorInterface $fileCreator
     */
    public function __construct(
        DataBuilderInterface $dataBuilder,
        DataFormatterInterface $dataFormatter,
        DataMergerInterface  $dataMerger,
        FileCreatorInterface $fileCreator
    ) {
        $this->dataBuilder = $dataBuilder;
        $this->dataFormatter = $dataFormatter;
        $this->dataMerger = $dataMerger;
        $this->fileCreator = $fileCreator;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        $translations = $this->dataBuilder->execute();
        $formattedTranslations = $this->dataFormatter->execute($translations);
        $mergedTranslations = $this->dataMerger->execute($formattedTranslations);

        return $this->fileCreator->execute($mergedTranslations);
    }
}