
# BetterGetData

Этот набор классов позволяет эффективно делать массовые действия в Битрикс24 посредством REST API. Позволяет массово: выгружать, обновлять, удалять, запускать бп.            
Классы соблюдают принципы **SOLID** .


## Безопасность

Внутри классов нет функционала остановки или ожидания в случае ``OPERATION TIME LIMIT`` или других подобных ошибках, в выше указанном случае класс пойдет по пути изложенном в пункте **обработка ошибок**.


### Обработка ошибок

При неудаче работа скрипта продолжится, но будет создан файл в папке с классами (если его еще нет) `report.json` в этом файле будет описание ошибки по каждому ID, пример
```json
{
    "ID сделки и Ошибка": {
        "100500": "Company is not found"
    }
}
```

## Порядок работы

Внутри параметров применяются все те же правила что и из документации.

Сами классы делают принты о том сколько батчей осталось и об начале времени ожидания, это поведение можно отключить в настройках. 

Так же в настройках можно настраивать единицы секунд ожидания (это время ожидания срабатывает на каждый второй батч) и количество единиц эелементов в каждом батче.

## Примеры
### Просто выгрузка

```php
<?php
require_once __DIR__ .'/vendor/autoload.php';

use Snappsisnappes\BetterGetData\BetterGetData;
use Snappsisnappes\BetterGetData\BetterGetDataSettings;

$Webhook = '';

$Settings = new BetterGetDataSettings();

$Method = 'crm.deal.list';

$Param = [
    'FILTER'=>[
        'COMPANY_ID'=>176,
        'CLOSED'=>'N',
    ],
    'SELECT'=>['ID']
];

$Data = BetterGetData::batch_getData($Param, $Webhook, $Method, $Settings);

print_r($Data);

/* 
(
    [0] => Array
        (
            [ID] => 10
        )

)
*/

```

### Обновление по списку ID применяя к ним один и тот же параметр полей
Предположим у нас есть список ID сделок и нам нужно проставить всем один и тот же набор полей,
например в поле комментарий проставить "123".
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Snappsisnappes\BetterGetData\BetterGetData;
use Snappsisnappes\BetterGetData\BetterGetDataSettings;
use Snappsisnappes\BetterGetData\BetterUpdateData;


$Webhook = '';

$BatchSize = 10;
$WaitPerEachSecondBatch = 5;
$Silent = True;

$Settings = new BetterGetDataSettings($BatchSize, $WaitPerEachSecondBatch, $Silent);

$Method = 'crm.deal.update';

$MainArray = [123]; // список ID

$Fields = ['COMMENTS' => '123'];

BetterUpdateData::BatchSingleFieldsManyIds($Fields, $Webhook, $MainArray, $Method, $Settings);

```
### Обновление по списку ID где у каждого есть свой набор полей для обновления
Предположим у нас есть список ID сделок и нам нужно проставить разным сделкам - разные поля.

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

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
    1=>['ID'=>11,'FIELDS'=>['COMMENTS' => '122']]
];

BetterUpdateData::BatchAllUnique($Webhook, $MainArray, $Method, $Settings);

```
### Массовый запуск бп по списку ID сделок
Предположим у нас есть список ID сделок по которому мы запустим один и тот же БП (можно передать разные ID бп).
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

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
```