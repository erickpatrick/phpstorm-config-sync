<?php namespace Nintendo\Translator;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Exception;
use PHPExcel_Worksheet_Column;
use PHPExcel_Worksheet_ColumnIterator;
use Nintendo\Translator\Interfaces\DataBuilderInterface;

class TranslationDataBuilder implements DataBuilderInterface
{
    const EXCLUDED_WORKSHEETS = ['C', 'D', 'E'];
    const EXCLUDED_COLUMNS_INDEXES = ['B', 'C', 'D'];

    private $excelFilename;

    /**
     * TranslationDataBuilder constructor.
     * @param string $excelFilename
     */
    public function __construct(string $excelFilename)
    {
        $this->excelFilename = $excelFilename;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        try {
            $spreadsheet = $this->getSpreadsheet();
        } catch (PHPExcel_Reader_Exception $exception) {
            echo $exception->getMessage();
            die();
        }

        $sheetNames = $this->getWorksheetsNames(
            $spreadsheet->getSheetNames()
        );

        $worksheets = $this->getWorksheets($spreadsheet, $sheetNames);
        $worksheetsColumns = $this->getWorksheetsColumns($worksheets);

        return $this->getAllTranslationsStrings($worksheetsColumns);
    }

    /**
     * @return PHPExcel
     * @throws PHPExcel_Reader_Exception
     */
    private function getSpreadsheet(): PHPExcel
    {
        try {
            $reader = PHPExcel_IOFactory::createReader(
                PHPExcel_IOFactory::identify($this->excelFilename)
            );

            return $reader->load($this->excelFilename);
        } catch (PHPExcel_Reader_Exception $exception) {
            throw new PHPExcel_Reader_Exception($exception->getMessage());
        }
    }

    /**
     * get the names of all worksheets but the ones from the excluded constant
     *
     * @param array $sheetsNames
     * @return array
     */
    private function getWorksheetsNames(array $sheetsNames): array
    {
        return $sheetsNames = array_filter(array_unique(array_map(function ($sheetName) {
            return in_array($sheetName[0], self::EXCLUDED_WORKSHEETS) ? $sheetName : '';
        }, $sheetsNames)));
    }

    /**
     * get worksheets from a spreadsheet given their names
     *
     * @param PHPExcel $spreadsheet
     * @param array $sheetNames
     * @return array
     */
    private function getWorksheets(PHPExcel $spreadsheet, array $sheetNames): array
    {
        return array_map(function ($sheetName) use ($spreadsheet) {
            return $spreadsheet->getSheetByName($sheetName);
        }, $sheetNames);
    }

    /**
     * @param array $worksheets
     * @return array
     */
    private function getWorksheetsColumns(array $worksheets): array
    {
        return array_map(function ($worksheet) {
            /** @var \PHPExcel_Worksheet $worksheet */
            return $worksheet->getColumnIterator();
        }, $worksheets);
    }

    /**
     * @param array $worksheetsColumns
     * @return array
     */
    private function getAllTranslationsStrings(array $worksheetsColumns): array
    {
        return array_map(function ($worksheetColumn) {
            return $this->getTranslationsStrings($worksheetColumn);
        }, $worksheetsColumns);
    }

    /**
     * @param PHPExcel_Worksheet_ColumnIterator $worksheetColumn
     * @return array
     */
    private function getTranslationsStrings(PHPExcel_Worksheet_ColumnIterator $worksheetColumn)
    {
        $translationsStrings = [];
        foreach ($worksheetColumn as $column) {
            if (!$this->isMetadataCell($column)) {
                $translationsStrings[$column->getColumnIndex()] = $this->getTranslatedStrings($column);
            }
        }

        return $translationsStrings;
    }

    /**
     * @param PHPExcel_Worksheet_Column $column
     * @return bool
     */
    private function isMetadataCell(PHPExcel_Worksheet_Column $column): bool
    {
        return in_array($column->getColumnIndex(), self::EXCLUDED_COLUMNS_INDEXES);
    }

    /**
     * @param PHPExcel_Worksheet_Column $column
     * @return array
     */
    private function getTranslatedStrings(PHPExcel_Worksheet_Column $column): array
    {
        $translations = [];
        foreach ($column->getCellIterator() as $cell) {
            $translations[] = (string)$cell;
        }

        return $translations;
    }
}