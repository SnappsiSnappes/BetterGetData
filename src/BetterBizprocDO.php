<?php
namespace Snappsisnappes\BetterGetData;
use Exception;
use Snappsisnappes\BetterGetData\BetterGetDataHelper;
use function in_array;


class BetterBizprocDO
{
    private int $DealId;
    private string $Method = 'bizproc.workflow.start';
    private int $TemplateId;
    private string $Entity;
    private array $Parameters;
    private array $DocumentId;

    private array $AllowedEntities = ['deal' => [0], 'company' => [1], 'lead' => [2]];
    public function __construct(int $DealId, int $TemplateId, string $Entity, array $Parameters)
    {
        $this->SetDealId($DealId);
        $this->SetTemplateId($TemplateId);
        $this->SetParameters($Parameters);
        $this->SetEntity($Entity);

    }

    public function GetDealId()
    {
        return $this->DealId;
    }

    public function GetMethod()
    {
        return $this->Method;
    }

    public function GetTemplateId()
    {
        return $this->TemplateId;
    }

    public function GetEntity()
    {
        return $this->Entity;
    }

    public function GetParameters()
    {
        return $this->Parameters;
    }

    public function SetDealId($Value)
    {
        $this->DealId = $Value;
    }


    public function SetTemplateId($Value)
    {
        $this->TemplateId = $Value;
    }

    public function SetEntity($Value)
    {

        $ValueConverted = mb_strtolower($Value, 'UTF-8');

        $AllowedEntities = $this->AllowedEntities;

        $AllowedEntitiesJson = json_encode($AllowedEntities, JSON_UNESCAPED_UNICODE);


        if (!isset($AllowedEntities[$ValueConverted])) {

            throw new Exception('Provided entity does not match any of records, given value ' . $ValueConverted . ' allowed entities: ' . $AllowedEntitiesJson, 500);

        }

        $this->DocumentId = [
            'crm',
            'CCrmDocument' . ucfirst($ValueConverted),
            strtoupper($ValueConverted) . '_' . $this->DealId,
        ];

        $this->Entity = $Value;
    }


    public function SetParameters($Value)
    {
        $this->Parameters = $Value;
    }

    public function GetMainArray()
    {

        return [
            'DealId' => $this->DealId,
            'BatchParams' => [
                'method' => $this->Method,
                'params' => [
                    'TEMPLATE_ID' => $this->TemplateId,
                    'DOCUMENT_ID' => $this->DocumentId,
                    'PARAMETERS' => $this->Parameters,
                ]
            ]
        ];
    }
}