<?php
namespace Snappsisnappes\BetterGetData;

use Exception;

class BetterGetDataSettings
{
    private $BatchSize;
    private $WaitPerEachSecondBatch;
    private $Silent = false;
    


    public function __construct(int $BatchSize = 10,int $WaitPerEachSecondBatch = 5,bool $Silent = false)
    {
        if($BatchSize > 50){
            throw new Exception('Размер батча может быть установлен в диапазоне от 1 до 50', 500);
        }

        $this->BatchSize = $BatchSize;
        $this->WaitPerEachSecondBatch = $WaitPerEachSecondBatch;
        $this->Silent = $Silent;
    }

    public function GetBatchSize()
    {
        return $this->BatchSize;
    }

    public function GetWaitPerEachSecondBatch()
    {
        return $this->WaitPerEachSecondBatch;
    }

    public function GetSilent()
    {
        return $this->Silent;
    }

}