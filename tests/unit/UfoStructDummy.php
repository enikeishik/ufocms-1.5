<?php
require_once 'classes/abstract/UfoStruct.php';

class UfoStructDummy extends UfoStruct
{
    public $fInt = 1;
    public $fFloat = 1.5;
    public $fString = 'test';
    public $fBoolean = true;
    public $fArray = null;
    public $fObject = null;
    public $fVariant;
}
