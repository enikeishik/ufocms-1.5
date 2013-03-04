<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * Класс-структура для хранения данных вставки.
 *
 * @author enikeishik
 *
 */
class UfoInsertionItemStruct extends UfoStruct
{
    public $Id = null;
    public $TargetId = null;
    public $PlaceId = null;
    public $OrderId = null;
    public $SourceId = null;
    public $SourcesIds = null;
    public $Title = null;
    public $ItemsStart = null;
    public $ItemsCount = null;
    public $ItemsLength = null;
    public $ItemsStartMark = null;
    public $ItemsStopMark = null;
    public $ItemsOptions = null;
}
