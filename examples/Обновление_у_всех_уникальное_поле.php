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

$MainArray = [
    0=>['ID'=>10,'FIELDS'=>['COMMENTS' => '121']]
];

BetterUpdateData::BatchAllUnique($Webhook, $MainArray, $Method, $Settings);

