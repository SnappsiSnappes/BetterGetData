<?php
require_once __DIR__.'/../vendor/autoload.php';

use Snappsisnappes\BetterGetData\BetterGetData;
use Snappsisnappes\BetterGetData\BetterGetDataSettings;

// Просто выгрузка

$Webhook = '';

$BatchSize = 10;
$WaitPerEachSecondBatch = 5;
$Silent = False;

$Settings = new BetterGetDataSettings($BatchSize, $WaitPerEachSecondBatch, $Silent);

$Method = 'crm.deal.list';

$Param = [
    'FILTER' => [
        'COMPANY_ID' => 10,
        'CLOSED' => 'N',
    ],
    'SELECT' => ['ID']
];

$Data = BetterGetData::batch_getData($Param, $Webhook, $Method, $Settings);

print_r($Data);

/* 
(
    [0] => Array
        (
            [ID] => 123
        )
    [1] => Array
        (
            [ID] => 124
        )

)
*/
