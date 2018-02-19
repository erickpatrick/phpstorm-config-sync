<?php namespace Nintendo\Translator\Magento;

use Nintendo\Translator\BaseTransformer;
use Nintendo\Translator\Interfaces\DataTransporter;

class Merger extends BaseTransformer
{
    /**
     * @param DataTransporter $transporter
     * @return DataTransporter
     */
    public function execute(DataTransporter $transporter): DataTransporter
    {
        $languagePairs = $this->createLanguagesPairs($transporter->getData());

        return $this->next($transporter, $this->removeDuplicatedEntries($languagePairs));
    }

    /**
     * we merge the English entries with another language entries so we
     * have something like "<english entry>":"<other language entry>"
     *
     * @param array $formattedTranslations
     * @return array
     */
    private function createLanguagesPairs(array $formattedTranslations): array
    {
        $languagePairs = [];

        foreach ($formattedTranslations as $column => $entries) {
            $languagePairs = array_merge_recursive($languagePairs, $this->getFlattenedLanguagesPair($entries));
        }

        return $languagePairs;
    }

    /**
     * get all the pairs for each EN-XX combination for all worksheet combined
     *
     * @param array $entries
     * @return array
     */
    private function getFlattenedLanguagesPair(array $entries): array
    {
        $languagePairs = [];
        $baseLanguage = array_shift($entries);

        foreach ($entries as $key => $entry) {
            $languagePairs = array_merge_recursive($languagePairs, $this->getLanguagesPair($baseLanguage, $entry));
        }

        return $languagePairs;
    }

    /**
     * @param array $baseLanguage
     * @param array $otherLanguage
     * @return array
     */
    private function getLanguagesPair(array $baseLanguage, array $otherLanguage): array
    {
        $languagePairs = [];
        $localeCode = "{$baseLanguage[1]}-{$otherLanguage[1]}";
        $entryLength = count($otherLanguage);

        for ($i = 0; $i < $entryLength; $i += 1) {
            $languagePairs[$localeCode][] = "\"{$baseLanguage[$i]}\",\"{$otherLanguage[$i]}\"";
        }

        return $languagePairs;
    }

    /**
     * We'll probably have duplicated entries for the pairs or even blank entries
     * since the spreadsheet has lots of blank entries. We need to remove them
     * so we can create proper and reliable translation files
     *
     * @param array $flattenLanguages
     * @return array
     */
    private function removeDuplicatedEntries(array $flattenLanguages): array
    {
        return array_map('array_unique', $flattenLanguages);
    }
}