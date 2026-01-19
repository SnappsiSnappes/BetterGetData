<?php
namespace Snappsisnappes\BetterGetData;

use Snappsisnappes\BetterGetData\BetterGetDataHelper;
use Snappsisnappes\BetterGetData\BetterGetDataSettings;

class BetterUpdateData
{


    /**
     * версия в которой 1 fields распространяется на весь массив с id-> main_array
     * <br> main array ```[0]=>123123,[1]=>123124 ```
     * <br> fields ```['COMMENTS'=>'lala'] ```
     */
    public static function BatchSingleFieldsManyIds(array $Fields, string $Webhook, array $MainArray, string $Method, BetterGetDataSettings $Settings)
    {

        $BatchSize = $Settings->GetBatchSize();
        $WaitPerEachSecondBatch = $Settings->GetWaitPerEachSecondBatch();
        $Silent = $Settings->GetSilent();


        $Total = count($MainArray);
        $Calls = ceil($Total / $BatchSize); # количество батчей, 51 = 2 батча, 100 = 2 батча 
        $CurrentCall = 0; // 0 может быть заголовком
        $BatchCounter = 0;
        $ArData = array();

        do {
            if (key_exists($CurrentCall, $MainArray)) {


                if (!!$MainArray[$CurrentCall]) {
                    $CurrentId = $MainArray[$CurrentCall];
                    $Temp = [
                        'method' => $Method,
                        'params' => [
                            'ID' => $CurrentId,
                            'fields' => $Fields
                        ]
                    ];
                    array_push($ArData, $Temp);
                }
            }


            if ($CurrentCall >= $Total or !!!$CurrentId) {

                BetterGetDataHelper::DebugPrint("\n попадание в финальный батч", $Settings);

                if (!!$ArData) {
                    BetterGetDataHelper::sendBatch($ArData, $Webhook, $Settings);
                }

                BetterGetDataHelper::DebugPrint("\n отправлен финальный батч", $Settings);

                break;
            } else {
                $CurrentCall++;
            }
            if ((count($ArData) == $BatchSize)) {
                $BatchCounter++;
                if ($BatchCounter % 2 == 0) {
                    sleep($WaitPerEachSecondBatch);

                    BetterGetDataHelper::DebugPrint("\n произошел сон... $WaitPerEachSecondBatch сек", $Settings);

                }
                BetterGetDataHelper::sendBatch($ArData, $Webhook, $Settings);
                $ArData = [];
                BetterGetDataHelper::DebugPrint("\n отправлен батч {$BatchCounter} из {$Calls} \n", $Settings);

            }
        } while ($BatchCounter <= $Calls);
        return;
    }

    /**
     * Версия в которой у каждого элемента свои fields
     * <br> main_array должен быть таким 
     * <br> ``` [0]=>['FIELDS'=>['...'],  'ID'=>123123 ] ```
     */
    public static function BatchAllUnique(string $Webhook, array $MainArray, string $Method, BetterGetDataSettings $Settings)
    {

        $BatchSize = $Settings->GetBatchSize();
        $WaitPerEachSecondBatch = $Settings->GetWaitPerEachSecondBatch();
        $Silent = $Settings->GetSilent();

        $Total = count($MainArray);
        $Calls = ceil($Total / $BatchSize); # количество батчей, 51 = 2 батча, 100 = 2 батча 
        $CurrentCall = 0; // 0 может быть заголовком
        $BatchCounter = 0;
        $ArData = array();

        do {
            if (key_exists($CurrentCall, $MainArray)) {


                if (!!$MainArray[$CurrentCall]) {

                    $CurrentId = $MainArray[$CurrentCall]['ID'];
                    $Temp = [
                        'method' => $Method,
                        'params' => [
                            'ID' => $CurrentId,
                            'fields' => $MainArray[$CurrentCall]['FIELDS']
                        ]
                    ];
                    array_push($ArData, $Temp);
                }
            }


            if ($CurrentCall >= $Total or !!!$CurrentId) {

                BetterGetDataHelper::DebugPrint("\n попадание в финальный батч ", $Settings);

                if (!!$ArData) {
                    BetterGetDataHelper::sendBatch($ArData, $Webhook, $Settings);
                }
                BetterGetDataHelper::DebugPrint("\n отправлен финальный батч ", $Settings);

                break;
            } else {
                $CurrentCall++;
            }
            if ((count($ArData) == $BatchSize)) {
                $BatchCounter++;
                if ($BatchCounter % 2 == 0) {
                    sleep($WaitPerEachSecondBatch);
                    BetterGetDataHelper::DebugPrint("\n произошел сон... $WaitPerEachSecondBatch сек", $Settings);
                }
                BetterGetDataHelper::sendBatch($ArData, $Webhook, $Settings);

                $ArData = [];

                BetterGetDataHelper::DebugPrint("\n отправлен батч {$BatchCounter} из {$Calls} \n", $Settings);


            }
        } while ($BatchCounter <= $Calls);
        return;
    }


}
