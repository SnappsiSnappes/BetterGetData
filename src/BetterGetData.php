<?php

namespace Snappsisnappes\BetterGetData;

use Snappsisnappes\BetterGetData\BetterGetDataHelper;

class BetterGetData
{


    public static function batch_getData(array $Params, string $Webhook,string $Method, BetterGetDataSettings $Settings)
    {
        $Ids = [];

        //# первые 50 записать в массив и узнать длинну списка total

        $Request = BetterGetDataHelper::bxRequest($Webhook, $Method, $Params);


        if (empty($Request)) {
            BetterGetDataHelper::DebugPrint("\n empty request", $Settings);
            return;
        }

        $Calls = ceil($Request['total'] / 50) - 1;
        foreach ($Request['result'] as $Item) {
            $Ids[] = $Item; // первая пачка
        }


        //# Запись оставшихся в массив

        $RequestCount = 0;
        $CallsCount = 0;
        while ($CallsCount < $Calls) {
            $CallsCount++;
            $Batch['cmd'][] = $Method . '?' . http_build_query(
                array_merge(
                    $Params,
                    ['start' => 50 * $CallsCount],
                )
            );

            if ($CallsCount % 50 == 0 or $CallsCount == $Calls) {
                if ($RequestCount % 2 == 0) {
                    sleep(2);
                }
                $Request = BetterGetDataHelper::bxRequest($Webhook, 'batch.json', $Batch);
                $Batch = array();
                $RequestCount++;

                foreach ($Request['result']['result'] as $Cmd) {
                    foreach ($Cmd as $Item) {
                        $Ids[] = $Item;
                    }
                }
            }
        }

        return $Ids;
    }

}
