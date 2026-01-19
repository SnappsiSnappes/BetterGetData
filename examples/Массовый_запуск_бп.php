<?php
require_once __DIR__.'/../vendor/autoload.php';

use Snappsisnappes\BetterGetData\BetterBizproc;
use Snappsisnappes\BetterGetData\BetterBizprocCollection;
use Snappsisnappes\BetterGetData\BetterBizprocDO;
use Snappsisnappes\BetterGetData\BetterGetData;
use Snappsisnappes\BetterGetData\BetterGetDataSettings;
use Snappsisnappes\BetterGetData\BetterUpdateData;


$Webhook = '';

$BatchSize = 10;
$WaitPerEachSecondBatch = 5;
$Silent = True;

$Settings = new BetterGetDataSettings($BatchSize, $WaitPerEachSecondBatch, $Silent);

$Ids = [10];

$Collection = new BetterBizprocCollection();

$BizprocTemplateId = 519;// id бп

$BizprocParameters = [];

foreach($Ids as $Id){
    $DealDO = new BetterBizprocDO($Id, $BizprocTemplateId, 'deal', $BizprocParameters);
    $Collection->Add($DealDO);
}

BetterBizproc::batch_bizproc($Webhook, $Collection, $Settings);