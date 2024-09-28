<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function saveDataToExcel($data, $spreadsheet)
{
    $sheetCount = $spreadsheet->getSheetCount();
    $sheetIndex = $sheetCount;
    $categories = [];
    $indexNextColumn = 1;
    $rowIndex = 2;
    $keysAllParametrs = [];

    foreach ($data as $product) {
        if (!in_array($product['category'], $categories)) {
            array_push($categories, $product['category']);
            // Creating a sheet
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Категория');
            $sheet->setCellValue('B1', 'Субкатегория');
            $sheet->setCellValue('C1', 'Ссылка на товар');
            $sheet->setCellValue('D1', 'Название Товара');
            $sheet->setCellValue('E1', 'Артикул');
            $sheet->setCellValue('F1', 'Цена');
            $sheet->setCellValue('G1', 'Наличие');
            $sheet->setCellValue('H1', 'Производитель');
            $sheet->setCellValue('I1', 'Картинки');
            $sheet->setCellValue('J1', 'Основные характеристики');
            $sheet->setCellValue('K1', 'Описание');
            $sheet->setCellValue('L1', 'Вес');
            $sheet->setCellValue('M1', 'Детальные характеристики');
            $indexNextColumn = 14;

            $sheet->getStyle('A1:GG1')->getFont()->setBold(true);
            $sheet->setTitle($product['category']);

            $sheetIndex++;
        } else {
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue("A$rowIndex", $product['category']);
            $sheet->setCellValue("B$rowIndex", $product['subCategory']);
            $sheet->setCellValue("C$rowIndex", $product['url']);
            $sheet->setCellValue("D$rowIndex", $product['title']);
            $sheet->setCellValue("E$rowIndex", $product['article']);
            $sheet->setCellValue("F$rowIndex", $product['price']);
            $sheet->setCellValue("G$rowIndex", $product['availability']);
            $sheet->setCellValue("H$rowIndex", $product['vendor']);
            $sheet->setCellValue("I$rowIndex", $product['images']);
            $sheet->setCellValue("J$rowIndex", $product['specification']);
            $sheet->setCellValue("K$rowIndex", $product['description']);
            $sheet->setCellValue("L$rowIndex", $product['weight']);
            $sheet->setCellValue("M$rowIndex", $product['detailedSpecification']);

            // Add filter parameters to the table
            if (count($product['filterParam']) > 0) {
                foreach ($product['filterParam'] as $key => $value) {
                    if (array_search($key, $keysAllParametrs) !== false) {
                        $indexColumn = (int)array_search($key, $keysAllParametrs) + $indexNextColumn;
                    } else {
                        $keysAllParametrs[] = $key;
                        $indexColumn = (int)array_search($key, $keysAllParametrs) + $indexNextColumn;
                    }
                    $sheet->setCellValue([$indexColumn, 1], $key);
                    $sheet->setCellValue([$indexColumn, $rowIndex], $value);
                }
            }
            $rowIndex++;
        }
    }

    $currentDate = date('d_m_Y_h_m_s');
    $writer = new Xlsx($spreadsheet);
    $writer->save('product_data' . $currentDate . '.xlsx');
}
