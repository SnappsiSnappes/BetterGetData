<?php
namespace Snappsisnappes\BetterGetData;
use Exception;
use Snappsisnappes\BetterGetData\BetterGetDataHelper;
use Snappsisnappes\BetterGetData\BetterBizprocDO;

class BetterBizprocCollection
{
    private array $Objects;

    public function __construct()
    {
    }

    public function Add(BetterBizprocDO $Item)
    {
        $this->Objects[] = $Item;
    }

    public function GetArrayOfObjects()
    {
        return $this->Objects;
    }
}