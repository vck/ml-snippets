<?php

include("mknn.php");

require 'vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Phpml\Metric\Accuracy;
use Phpml\Metric\ConfusionMatrix;
use Phpml\CrossValidation\RandomSplit;

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

$spreadsheet = $reader->load("target.csv");

$sheetData = $spreadsheet->getActiveSheet()->toArray();

$splited_datasets = train_test_split($sheetData, 0.8);
$train = $splited_datasets[0];
$test = $splited_datasets[1];
$train_validity = validity($train, 3);
$y_test = data_class($test);

$predicted = weight_voting($train, $test, $train_validity, 3);

print_r(ConfusionMatrix::compute($y_test, $predicted));
print_r(Accuracy::score($y_test, $predicted));

?>
