<?php

namespace Snappsisnappes\BetterGetData;


class BetterGetDataHelper
{


    public static function bxRequest(string $Webhook, string $Method, $PostFields = null)
    {
        $PostFields = http_build_query($PostFields);
        $Curl = curl_init();
        curl_setopt_array(
            $Curl,
            array(
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTREDIR => 10,
                CURLOPT_URL => $Webhook . $Method,
            )
        );
        if (!is_null($PostFields))
            curl_setopt($Curl, CURLOPT_POSTFIELDS, $PostFields);

        $Result = json_decode(curl_exec($Curl), true);
        curl_close($Curl);

        return $Result;
    }

    public static function HandleBatchErrors($Result, $ArData, BetterGetDataSettings $Settings)
    {
        $ErrorsList = $Result['result']['result_error'];
        foreach ($ErrorsList as $ErrorsKey => $ErrorsVal) {

            if (!!$ErrorsVal['error_description']) {

                $JsonString = file_get_contents(__DIR__ . '/report.json');
                $Data = json_decode($JsonString, true);


                $Id = str_replace('COMPANY_', '', $ArData[$ErrorsKey]['params']['DOCUMENT_ID'][2]);
                $Data['ID сделки и Ошибка'][$Id] = $ErrorsVal['error_description'];

                // Encode it back to JSON and write it to the file
                $JsonContent = json_encode($Data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                file_put_contents(__DIR__ . '/report.json', $JsonContent);

                self::DebugPrint("\n ---- Поймал ошибки, отчёт записан в json", $Settings);

            } else {
                break;
            }
        }
    }

    public static function sendBatch($ArData, string $Webhook, BetterGetDataSettings $Settings)
    {
        foreach ($ArData as $Key => $Data) {
            $ArDataRest['cmd'][$Key] = $Data['method'];
            if (!empty($Data['params'])) {
                $ArDataRest['cmd'][$Key] .= '?' . http_build_query($Data['params']);
            }
        }
        $ArDataRest['halt'] = 0; //integer 0 or 1 stop batch on error

        $Result = self::bxRequest($Webhook, 'batch.json', $ArDataRest);

        /* catching erros, wrting to report json */
        self::HandleBatchErrors($Result, $ArData, $Settings);
    }

    public static function CustomSendBatch($ArData, string $Webhook, BetterGetDataSettings $Settings)
    {
        foreach ($ArData as $Key => $Data) {
            $ArDataRest['cmd'][$Key] = $Data['method'];
            if (!empty($Data['params'])) {
                $ArDataRest['cmd'][$Key] .= '?' . http_build_query($Data['params']);
            }
        }
        $ArDataRest['halt'] = 0; //integer 0 or 1 stop batch on error

        $Result = self::bxRequest($Webhook, 'batch.json', $ArDataRest);

        /* catching erros, wrting to report json */
        self::HandleBatchErrors($Result, $ArData, $Settings);


        return $Result['result']['result'];
    }


    /**
     * Версия в которой у каждого элемента свои fields
     * <br> main_array должен быть таким 
     * <br> ``` [0]=>['FIELDS'=>['...'], 'METHOD'=>'crm...', 'ID'=>123123 ] ```
     */
    public static function CustomBatch(string $Webhook, array $MainArray, BetterGetDataSettings $Settings)
    {
        $BatchSize = $Settings->GetBatchSize();
        $WaitPerEachSecondBatch = $Settings->GetWaitPerEachSecondBatch();
        $Silent = $Settings->GetSilent();


        $Total = count($MainArray);
        $Calls = ceil($Total / $BatchSize); # количество батчей, 51 = 2 батча, 100 = 2 батча 
        $CurrentCall = 0; // 0 может быть заголовком
        $BatchCounter = 0;
        $ArData = [];
        $Final = [];

        do {
            if (key_exists($CurrentCall, $MainArray)) {


                if (!!$MainArray[$CurrentCall]) {

                    $CurrentId = $MainArray[$CurrentCall]['ID'];
                    $temp = [
                        'method' => $MainArray[$CurrentCall]['METHOD'],
                        'params' => [
                            'ID' => $MainArray[$CurrentCall]['ID'],
                            'fields' => $MainArray[$CurrentCall]['FIELDS']
                        ]
                    ];
                    array_push($ArData, $temp);
                }
            }


            if ($CurrentCall >= $Total or !!!$CurrentId) {
                self::DebugPrint("\n попадание в финальный батч", $Settings);

                if (!!$ArData) {
                    $Final = array_merge(BetterGetDataHelper::CustomSendBatch($ArData, $Webhook, $Settings), $Final);
                }
                self::DebugPrint("\n отправлен финальный батч", $Settings);

                break;
            } else {
                $CurrentCall++;
            }
            if ((count($ArData) == $BatchSize)) {
                $BatchCounter++;
                if ($BatchCounter % 2 == 0) {
                    sleep($WaitPerEachSecondBatch);

                    self::DebugPrint("\n произошел сон... $WaitPerEachSecondBatch сек", $Settings);

                }
                $Final = array_merge(BetterGetDataHelper::CustomSendBatch($ArData, $Webhook, $Settings), $Final);

                $ArData = [];

                self::DebugPrint("\n отправлен батч {$BatchCounter} из {$Calls} \n", $Settings);

            }
        } while ($BatchCounter <= $Calls);
        return $Final;
    }

    public static function DebugPrint(string $Msg, BetterGetDataSettings $Settings)
    {
        if (!$Settings->GetSilent()) {
            echo $Msg;
        }
    }

}
