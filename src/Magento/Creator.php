<?php namespace Nintendo\Translator\Magento;

use Nintendo\Translator\BaseTransformer;
use Nintendo\Translator\Interfaces\DataTransporter;

class Creator extends BaseTransformer
{

    /**
     * @param DataTransporter $transporter
     * @return DataTransporter
     */
    public function execute(DataTransporter $transporter): DataTransporter
    {
        $filenames = [];

        foreach ($transporter->getData() as $filename => $fileContent) {
            $filenames[] = $this->writeToFile($filename, $fileContent);
        }

        return $transporter->setData($filenames);
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