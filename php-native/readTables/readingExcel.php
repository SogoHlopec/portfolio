<?php
require __DIR__ . '/vendor/autoload.php';

function readExcel($filePath)
{

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($filePath);

    $sheet = $spreadsheet->getActiveSheet();

    $data = [];
    $headerRow = $sheet->getRowIterator()->current();
    $headers = [];

    foreach ($headerRow->getCellIterator() as $cell) {
        $headers[] = $cell->getValue();
    }

    foreach ($sheet->getRowIterator(2) as $row) {
        $product = [];
        foreach ($row->getCellIterator() as $key => $cell) {
            $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cell->getColumn());
            $product[] = ["title" => $headers[$columnIndex - 1], "value" => $cell->getValue()];
        }
        $data[] = $product;
    }
    return $data;
}

$data = readExcel("data.xlsx");
var_dump($data);
