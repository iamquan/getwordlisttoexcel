<?php

$startTime = microtime(true);

require "vendor/autoload.php";

use DiDom\Document;
use DiDom\Query;

//error handler function
function customError($errno, $errstr)
{
    echo PHP_EOL . "<b>Error:</b> [$errno] $errstr<br>";
    echo PHP_EOL . "Ending Script" . PHP_EOL;
    die();
}

//set error handler
set_error_handler("customError");

$lettersUrl = 'http://www.oxfordlearnersdictionaries.com/browse/english/';
$letters = new Document($lettersUrl, true);

$wordsUrl = array();

foreach ($letters->find('#letters a') as $letterUrl) {

    $groupResults = new Document($letterUrl->getAttribute('href'), true);

    foreach ($groupResults->find('#groupResult a') as $groupResult) {
        $result = new Document($groupResult->getAttribute('href'), true);

        foreach ($result->find('#result a') as $wordUrl) {
            $wordsUrl[] = $wordUrl->getAttribute('href');
        }
    }
}

$totalFrequentlyWords = 0;
$totalFrequentlyDefinitions = 0;
foreach ($wordsUrl as $wordUrl) {

    if ($wordUrl != 'http://www.oxfordlearnersdictionaries.com/definition/english/nancy-drew') { //this link is NOT FOUND

        $wordDocument = new Document($wordUrl, true);

        if (count($elements = $wordDocument->find('.h')) != 0) { //check if word has name
            $entryName = $wordDocument->find('.h')[0]->text();
        } else {
            $entryName = '';
        }

        if (count($elements = $wordDocument->find('.webtop-g .oxford3000')) != 0) {
            $totalFrequentlyWords++;
            $totalFrequentlyDefinitions += count($wordDocument->find('.sn-g .oxford3000'));

            echo 'Current entry: ' . $entryName . PHP_EOL;
        }
    }
}

echo 'Total frequently words: ' . $totalFrequentlyWords . PHP_EOL;
echo 'Total frequently definitions: ' . $totalFrequentlyDefinitions . PHP_EOL;
echo 'Statistics from Oxford Advanced Learner’s Dictionary' . PHP_EOL;

$endTime = microtime(true);
$executionTime = ($endTime - $startTime) / 60;

echo 'Total Execution Time: ' . $executionTime . ' minutes' . PHP_EOL;

exit;
