<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * Класс-структура для хранения данных раздела.
 *
 * @author enikeishik
 *
 */
class UfoDebugStruct extends UfoStruct
{
    public $message = '';
    public $scriptTime = 0.0;
    public $blockTime = 0.0;
    public $memoryUsed = 0;
    public $memoryUsedTotal = 0;
    public $className = '';
    public $methodName = '';
    public $lineNumber = 0;
    public $dbQuery = '';
    public $dbError = '';
    public $callStack = '';
}
