<?php
require_once 'classes/abstract/UfoStruct.php';

/**
 * Класс-структура для хранения данных вставки.
 *
 * @author enikeishik
 *
 */
class UfoInsertionStruct extends UfoStruct
{
    public $targetId = null;
    public $placeId = null;
    public $orderId = null;
    public $sourceId = null;
    public $sourcesIds = null;
    public $title = null;
    public $itemsStart = null;
    public $itemsCount = null;
    public $itemsLength = null;
    public $itemsStartMark = null;
    public $itemsStopMark = null;
    public $itemsOptions = null;
}
