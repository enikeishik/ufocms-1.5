<?php
require_once 'UfoModuleInterface.php';

interface UfoIneractiveModuleInterface extends UfoModuleInterface
{
    public function add(IItem $item);
}
