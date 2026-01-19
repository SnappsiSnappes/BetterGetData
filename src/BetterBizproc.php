<?php
namespace Snappsisnappes\BetterGetData;
use Snappsisnappes\BetterGetData\BetterGetDataHelper;
use Snappsisnappes\BetterGetData\BetterBizprocDO;
use Snappsisnappes\BetterGetData\BetterBizprocCollection;
use function in_array;

class BetterBizproc
{

    public static function batch_bizproc($Webhook, BetterBizprocCollection $Collection, BetterGetDataSettings $Settings)
    {
        $WaitPerEachSecondBatch = $Settings->GetWaitPerEachSecondBatch();
        $MainArray = $Collection->GetArrayOfObjects();

        $BatchSize = 5;
        $Total = count($MainArray) - 1;
        $Calls = ceil($Total / $BatchSize); # количество батчей, 51 = 2 батча, 100 = 2 батча 
        $CurrentCall = 0; // 0 может быть заголовком
        $BatchCounter = 0;
        $ArData = [];

        do {
            if (key_exists($CurrentCall, $MainArray)) {
                if (!!$MainArray[$CurrentCall]) {

                    $CurrentObject = $MainArray[$CurrentCall]->GetMainArray();

                    $CurrentId = $CurrentObject['DealId'];

                    $BatchParams = $CurrentObject['BatchParams'];

                    array_push($ArData, $BatchParams);
                }
            }

            if ($CurrentCall >= $Total or !!!$CurrentId) {

                BetterGetDataHelper::DebugPrint("\n попадание в финальный батч", $Settings);

                if (!!$ArData) {
                    BetterGetDataHelper::DebugPrint("\n arData пустой", $Settings);

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
                    BetterGetDataHelper::DebugPrint("\n произошел сон... $WaitPerEachSecondBatch сек", $Settings);
                    sleep($WaitPerEachSecondBatch);
                }
                BetterGetDataHelper::sendBatch($ArData, $Webhook, $Settings);
                $ArData = [];
                BetterGetDataHelper::DebugPrint("\n отправлен батч {$BatchCounter} из {$Calls}", $Settings);

            }
        } while ($BatchCounter <= $Calls);
        return;
    }
}
?>