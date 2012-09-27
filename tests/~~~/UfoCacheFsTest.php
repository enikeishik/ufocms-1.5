<?php

use Codeception\Util\Stub;

class UfoCacheFsTest extends \Codeception\TestCase\Test
{
   /**
    * @var CodeGuy
    */
    protected $codeGuy;

    // keep this setupUp and tearDown to enable proper work of Codeception modules
    protected function setUp()
    {
        if ($this->bootstrap) require $this->bootstrap;
        $this->dispatcher->dispatch('test.before', new \Codeception\Event\Test($this));
        $this->codeGuy = new CodeGuy($scenario = new \Codeception\Scenario($this));
        $scenario->run();

        // initialization code
    }

    protected function tearDown()
    {
        $this->dispatcher->dispatch('test.after', new \Codeception\Event\Test($this));
    }
    /*
    public function getTrace()
    {
        
    }
    */

    // tests

    public $class = 'UfoCacheFs';
    
    public function testCacheLoad(CodeGuy $I)
    {
        $cachePath = $_SERVER['DOCUMENT_ROOT'] . '/tmp';
        $cacheFileExt = 'txt';
        $cache = new UfoCacheFs('a', 
                                array('CachePath'    => $cachePath, 
                                      'CacheFileExt' => $cacheFileExt));
        $I->executeMethod($cache, 'load');
    }
}
