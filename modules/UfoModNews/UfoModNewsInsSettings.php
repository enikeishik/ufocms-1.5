<?php
require_once 'classes/abstract/UfoInsertionItemSettings.php';
/**
 * Класс-структура для хранения данных установок модуля и дополнительных данных вставки.
 * @author enikeishik
 */
class UfoModNewsInsSettings extends UfoInsertionItemSettings
{
    public $IconAttributes = '';
    public $AnnounceLength = 0;
    public $TimerOffset = 0;
    public $itemsCount = 0;
    public $itemNumber = 0;
}
