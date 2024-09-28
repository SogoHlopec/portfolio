<?php
function saveDataToYml($data, $xml, $shop)
{
    $currencies = $xml->createElement('currencies');
    $shop->appendChild($currencies);

    $currency  = $xml->createElement('currency');
    $currency->setAttribute('id', 'RUR');
    $currency->setAttribute('rate', '1');
    $currencies->appendChild($currency);

    $offers = $xml->createElement('offers');
    $shop->appendChild($offers);

    foreach ($data as $index => $dataProduct) {
        $offer = $xml->createElement('offer');
        $offer->setAttribute('id', "{$dataProduct['article']}");
        $offers->appendChild($offer);

        $name = $xml->createElement('name', $dataProduct['title']);
        $offer->appendChild($name);

        $categoryId = $xml->createElement('categoryId', $dataProduct['subCategoryId']);
        $offer->appendChild($categoryId);

        $price = $xml->createElement('price', $dataProduct['price']);
        $offer->appendChild($price);

        $vendor = $xml->createElement('vendor', $dataProduct['vendor']);
        $offer->appendChild($vendor);

        $currencyId = $xml->createElement('currencyId', 'RUR');
        $offer->appendChild($currencyId);

        $description = $xml->createElement('description', $dataProduct['description']);
        $offer->appendChild($description);


        if ($dataProduct['detailedSpecification'] !== 'Не найдено') {
            $specifications = $xml->createElement('specifications', ($dataProduct['specification'] . PHP_EOL . $dataProduct['detailedSpecification']));
            $offer->appendChild($specifications);
        } else {
            $specifications = $xml->createElement('specifications', $dataProduct['specification']);
            $offer->appendChild($specifications);
        }

        $vendorCode = $xml->createElement('vendorCode', $dataProduct['article']);
        $offer->appendChild($vendorCode);

        $images = explode('; ', $dataProduct['images']);
        foreach ($images as $index => $img) {
            $count = $index + 1;
            $picture = $xml->createElement("picture{$count}", $img);
            $offer->appendChild($picture);
        }

        $count = $xml->createElement('count', $dataProduct['availability']);
        $offer->appendChild($count);

        $weight = $xml->createElement('weight', $dataProduct['weight']);
        $offer->appendChild($weight);

        if (array_key_exists('filterParam', $dataProduct)) {
            foreach ($dataProduct['filterParam'] as $key => $value) {
                $param = $xml->createElement('param', $value);
                $param->setAttribute('name', $key);
                $offer->appendChild($param);
            }
        }
    }
}

function saveToYml($dataCategories, $dataProducts)
{

    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;

    $ymlCatalog = $xml->createElement('yml_catalog');
    date_default_timezone_set('Europe/Minsk');
    $ymlCatalog->setAttribute('date', date('Y-m-d H:i'));
    $xml->appendChild($ymlCatalog);

    $shop = $xml->createElement('shop');
    $ymlCatalog->appendChild($shop);

    if (count($dataProducts) > 0) {
        saveDataToYml($dataProducts, $xml, $shop);
    }

    $xmlFilePath = 'data.xml';
    $xml->save($xmlFilePath);
}
