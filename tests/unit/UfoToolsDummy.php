<?php
require_once 'classes/UfoTools.php';

class UfoToolsDummy
{
    use UfoTools;
    
    private $config = null;
    
    public function __construct()
    {
        $this->config = new UfoConfig();
    }
}
