<?php namespace Nintendo\Translator\Magento;

use Nintendo\Translator\BaseTransformer;
use Nintendo\Translator\Interfaces\DataTransporter;

class Formatter extends BaseTransformer
{
    const CONCATENATE_REFERENCE_VALUE = 'concatenate';
    const DYNAMIC_CONTENT_STRING = 'XX';
    const STRING_PAIR_ADJUSTMENTS = [
        '/\ \.\ /'  => '. ',
        '/\%1 \./' => '%1. ',
        '/\%2 \./' => '%2. ',
        '/\ \./'   => '. ',
        '/\ \!/'   => '! ',
        '/\ \?/'   => '? ',
        '/\ \ /'   => ' ',
    ];

    /**
     * takes as text parts and concatenates them into full sentences
     *
     * @param DataTransporter $transporter
     * @return DataTransporter
     */
    public function execute(DataTransporter $transporter): DataTransporter
    {
        return $this->next($transporter, array_map(function ($translation) {
            return $this->getConcatenatedStrings($translation);
        }, $transporter->getData()));
    }

    /**
     * based on the criteria presented in the file, we concatenate the strings to
     * form the full text string that will be used by Magento as the replacement
     * for the text that needs to be translated into a given language.
     *
     * Complexity: O(n*n) - unfortunately, could not find a better way to achieve
     * this without making this class stateful, thus, kept is as is making it
     * stateless/immutable
     *
     * @param array $translation
     * @return array
     */
    private function getConcatenatedStrings(array $translation): array
    {
        $translationsTexts = [];
        $finalTranslationText = '';
        $referenceEntries = array_shift($translation);
        $dynamicCounter = 1;

        foreach ($translation as $columnName => $columnCells) {
            foreach ($columnCells as $cellKey => $cellContent) {
                list($cellContent, $dynamicCounter) = $this->addPlaceholders($cellContent, $dynamicCounter);

                if ($this->isStopCell($cellContent, $referenceEntries, $cellKey)) {
                    $translationsTexts[$columnName][] = $this->adjustPunctuationAndSpaces($finalTranslationText);
                    $finalTranslationText = '';
                    $dynamicCounter = 1;
                    continue;
                }

                if ($this->shouldConcatenateString($referenceEntries, $cellKey)) {
                    $finalTranslationText .= "{$cellContent}";
                } else {
                    $translationsTexts[$columnName][] = $this->adjustPunctuationAndSpaces($cellContent);
                }
            }
        }

        return $translationsTexts;
    }

    /**
     * changes the XXX and XX.XX entries into placeholders Magento can understand
     * and use to set dynamic values
     *
     * @param $cellContent
     * @param $dynamicContentCounter
     * @return array
     */
    private function addPlaceholders($cellContent, $dynamicContentCounter): array
    {
        $cellContent = trim((string)$cellContent);

        return $this->adjustsDynamicValues($cellContent, $dynamicContentCounter);
    }

    /**
     * If a cell is of dynamic content type, we add spacing around it, otherwise
     * we can end up with things like '<text>%1<text>'. For each new dynamic
     * entry in the sentence we increase the counter so the parameters
     * from sprintf are taken into account and in the right order
     *
     * @param $cellContent
     * @param $dynamicContentCounter
     * @return array
     */
    private function adjustsDynamicValues($cellContent, $dynamicContentCounter): array
    {
        if ($this->isDynamicContent($cellContent)) {
            $cellContent = " %{$dynamicContentCounter} ";
            $dynamicContentCounter++;
        }

        return [$cellContent, $dynamicContentCounter];
    }

    /**
     * Checks whether we should stop concatenating cells given the right
     * conditions for it (cell is empty and reference entry does not
     * tell to concatenate)
     *
     * @param $cellContent
     * @param $referenceEntries
     * @param $cellKey
     * @return bool
     */
    private function isStopCell($cellContent, $referenceEntries, $cellKey): bool
    {
        return empty($cellContent) && $referenceEntries[$cellKey] !== self::CONCATENATE_REFERENCE_VALUE;
    }

    /**
     * Cleans data to avoid having punctuation surrounded by spaces or far from the
     * previous word; dynamic values surrounded by empty spaces; or double spaces
     *
     * @param string $finalTranslationText
     * @return string
     */
    private function adjustPunctuationAndSpaces(string $finalTranslationText): string
    {
        foreach (self::STRING_PAIR_ADJUSTMENTS as $search => $replacement) {
            $finalTranslationText = preg_replace($search, $replacement, $finalTranslationText);
        }

        return trim($finalTranslationText);
    }

    /**
     * Checks whether cell content is of a dynamic content type or not
     *
     * @param $cellContent
     * @return bool
     */
    private function isDynamicContent($cellContent): bool
    {
        return strpos($cellContent, self::DYNAMIC_CONTENT_STRING) !== false;
    }

    /**
     * checks whether current string should be concatenated to the previous
     * one or not
     *
     * @param $referenceEntries
     * @param $cellKey
     * @return bool
     */
    private function shouldConcatenateString($referenceEntries, $cellKey): bool
    {
        return $referenceEntries[$cellKey] === self::CONCATENATE_REFERENCE_VALUE;
    }
}