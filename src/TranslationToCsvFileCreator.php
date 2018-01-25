<?php namespace Nintendo\Translator;

use Nintendo\Translator\Interfaces\FileCreatorInterface;

class TranslationToCsvFileCreator implements FileCreatorInterface
{

    /**
     * @param array $content
     * @return array
     */
    public function execute(array $content): array
    {
        $filenames = [];

        foreach ($content as $filename => $fileContent) {
            $filenames[] = $this->writeToFile($filename, $fileContent);
        }

        return $filenames;
    }

    /**
     * writes each translation string to its relative file
     *
     * @param string $filename
     * @param array $fileContent
     * @return string
     */
    private function writeToFile(string $filename, array $fileContent): string
    {
        $filename = $this->getCleanFilename($filename);

        $handle = fopen($filename, 'w');

        foreach($fileContent as $line) {
            fputcsv($handle, [$line], ",", ' ');
        }

        fclose($handle);

        return $filename;
    }

    /**
     * generates the proper csv filename
     *
     * @param string $filename
     * @return string
     */
    private function getCleanFilename(string $filename): string
    {
        $filename = strtolower(explode(' ', $filename)[0]);

        return "generated/{$filename}.csv";
    }
}