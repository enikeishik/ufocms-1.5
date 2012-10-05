<?php

class UfoCacheFsCest
{
    public $class = 'UfoCacheFs';
    
    private $testData = 'some test data';
    private $testDataHash = 'a';
    private $testLargeData = '';
    private $testLargeDataHash = 'large';
    private $testNonExistingHash = 'b';
    private $testComplexHash = 'сложный хэш + .,;:\'"/\\[]()-_=*&^%$#@!~`1234567890|?';
    private $cacheSettings = array('Path'     => 'c:\tmp', 
                                   'FileExt'  => 'txt', 
                                   'Lifetime' => 10);
    
    function __construct() {
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
        $I->wantTo('save some data into cache with hash `' . $this->testDataHash . '`');
        $cache = new UfoCacheFs($this->testDataHash, $this->cacheSettings);
        $I->executeMethod($cache, 'save', $this->testData);
    }
    
    public function saveLargeData(\CodeGuy $I) {
        $I->wantTo('save some large data into cache with hash `' . $this->testLargeDataHash . '`');
        $cache = new UfoCacheFs($this->testLargeDataHash, $this->cacheSettings);
        $I->executeMethod($cache, 'save', $this->testLargeData);
    }
    
    public function saveComplexHash(\CodeGuy $I) {
        $I->wantTo('save some data into cache with complex hash `' . $this->testComplexHash . '`');
        $cache = new UfoCacheFs($this->testComplexHash, $this->cacheSettings);
        $I->executeMethod($cache, 'save', $this->testData);
    }
    
    // Tests for UfoCacheFs.load
    
    public function loadTest(\CodeGuy $I) {
        $I->wantTo('load some data from cache with hash `' . $this->testDataHash . '`');
        $cache = new UfoCacheFs($this->testDataHash, $this->cacheSettings);
        $I->executeMethod($cache, 'load');
    }
    
    public function loadLargeData(\CodeGuy $I) {
        $I->wantTo('load some large data from cache with hash `' . $this->testLargeDataHash . '`');
        $cache = new UfoCacheFs($this->testLargeDataHash, $this->cacheSettings);
        $I->executeMethod($cache, 'load');
    }
    
    public function loadComplexHash(\CodeGuy $I) {
        $I->wantTo('load some data from cache with complex hash `' . $this->testComplexHash . '`');
        $cache = new UfoCacheFs($this->testComplexHash, $this->cacheSettings);
        $I->executeMethod($cache, 'load');
    }
    
    // Compare cached data with original
    public function compareLoadWithSave(\CodeGuy $I) {
        $I->wantTo('load data and compare it with saved data');
        $cache = new UfoCacheFs($this->testDataHash, $this->cacheSettings);
        $I->seeMethodReturns($cache, 'load', $this->testData);
    }
    
    // Test for UfoCacheFs.load for non existing cache
    public function loadNonExisting(\CodeGuy $I) {
        $I->wantTo('load some data from cache with non existing hash `' . $this->testNonExistingHash . '`');
        $cache = new UfoCacheFs($this->testNonExistingHash, $this->cacheSettings);
        $I->executeMethod($cache, 'load');
        $I->seeMethodReturns($cache, 'load', '');
    }
}
