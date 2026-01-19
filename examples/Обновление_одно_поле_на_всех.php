<?php
require_once __DIR__.'/../vendor/autoload.php';

use Snappsisnappes\BetterGetData\BetterGetData;
use Snappsisnappes\BetterGetData\BetterGetDataSettings;
use Snappsisnappes\BetterGetData\BetterUpdateData;


$Webhook = '';

$BatchSize = 10;
$WaitPerEachSecondBatch = 5;
$Silent = True;

$Settings = new BetterGetDataSettings($BatchSize, $WaitPerEachSecondBatch, $Silent);

$Method = 'crm.deal.update';

$MainArray = [10];

$Fields = ['COMMENTS' => '123'];

BetterUpdateData::BatchSingleFieldsManyIds($Fields, $Webhook, $MainArray, $Method, $Settings);

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
