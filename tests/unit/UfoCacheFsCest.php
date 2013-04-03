<?php
require_once 'Tools.php';

class UfoCacheFsCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    private $root = '';
    private $testData = 'some test data';
    private $testDataHash = 'a';
    private $testLargeData = '';
    private $testLargeDataHash = 'large';
    private $testNonExistingHash = 'b';
    private $testComplexHash = 'сложный хэш + .,;:\'"/\\[]()-_=*&^%$#@!~`1234567890|?';
    private $cacheSettings = null;
    
    function __construct() {
        $this->root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $this->root . self::DS . 'config.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCacheFs.php';
        //$config = new UfoConfig(array('cacheFsDir' => $this->root . self::DS . '_cache'));
        $config = new UfoConfig();
        $this->cacheSettings = $config->cacheFsSettings;
        
        $s = '';
        for ($i = 0; $i < 256; $i++) {
            $s .= chr($i);
        }
        for ($i = 0; $i < 256; $i++) {
            $this->testLargeData .= $s . "\r\n\r\n";
        }
    }
    
    // Tests for UfoCacheFs.save
    
    public function saveTest(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('save some data into cache with hash `' . $this->testDataHash . '`');
        $cache = new UfoCacheFs($this->testDataHash, $this->cacheSettings);
        $I->executeMethod($cache, 'save', $this->testData);
    }
    
    public function saveLargeData(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('save some large data into cache with hash `' . $this->testLargeDataHash . '`');
        $cache = new UfoCacheFs($this->testLargeDataHash, $this->cacheSettings);
        $I->executeMethod($cache, 'save', $this->testLargeData);
    }
    
    public function saveComplexHash(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('save some data into cache with complex hash `' . $this->testComplexHash . '`');
        $cache = new UfoCacheFs($this->testComplexHash, $this->cacheSettings);
        $I->executeMethod($cache, 'save', $this->testData);
    }
    
    // Tests for UfoCacheFs.load
    
    public function loadTest(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('load some data from cache with hash `' . $this->testDataHash . '`');
        $cache = new UfoCacheFs($this->testDataHash, $this->cacheSettings);
        $I->executeMethod($cache, 'load');
    }
    
    public function loadLargeData(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('load some large data from cache with hash `' . $this->testLargeDataHash . '`');
        $cache = new UfoCacheFs($this->testLargeDataHash, $this->cacheSettings);
        $I->executeMethod($cache, 'load');
    }
    
    public function loadComplexHash(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('load some data from cache with complex hash `' . $this->testComplexHash . '`');
        $cache = new UfoCacheFs($this->testComplexHash, $this->cacheSettings);
        $I->executeMethod($cache, 'load');
    }
    
    // Compare cached data with original
    public function compareLoadWithSave(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('load data and compare it with saved data');
        $cache = new UfoCacheFs($this->testDataHash, $this->cacheSettings);
        $I->seeMethodReturns($cache, 'load', $this->testData);
    }
    
    // Test for UfoCacheFs.load for non existing cache
    public function loadNonExisting(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('load some data from cache with non existing hash `' . $this->testNonExistingHash . '`');
        $cache = new UfoCacheFs($this->testNonExistingHash, $this->cacheSettings);
        $I->executeMethod($cache, 'load');
        $I->seeMethodReturns($cache, 'load', '');
    }
}
