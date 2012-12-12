<?php
require_once 'classes/UfoDbSettings.php';
require_once 'classes/UfoCacheFsSettings.php';

final class UfoConfig
{
    private $_debug = true;
    
    private $_directorySeparator = DIRECTORY_SEPARATOR;
    
    private $_siteRoot = __DIR__;
    
    private $_classesDir = 'classes';
    private $_modulesDir = 'modules';
    private $_templatesDir = 'templates';
    
    private $_dbHost = 'db4free.net';
    private $_dbLogin = 'ufocms';
    private $_dbPassword = 'hi98tgIYGV98g';
    private $_dbName = 'ufocms';
    /*
    private $_dbHost = 'sql09.freemysql.net';
    private $_dbLogin = 'lifehacker';
    private $_dbPassword = 'ahbvecrekm';
    private $_dbName = 'lifehacker';
    
    private $_dbHost = 's16dl1.royaltelesystems.net';
    private $_dbLogin = 'fazlyeva_ufocms';
    private $_dbPassword = 'i87fcweywfdJYFGV';
    private $_dbName = 'fazlyeva_ufocms';
    */
    
    private $_dbTablePrefix = 'ufo_';
    
    private $_phpVersionRequired = array('Major'   => 5, 
                                         'Minor'   => 4, 
                                         'Release' => 0);
    
    private $_cacheLifetime = 3;
    private $_cacheFsDir = '/_cache';
    private $_cacheFsFileExt = '.htm';
    private $_cacheFsSavetime = 3600;
    
    private $_dbSettings = null;
    private $_cacheFsSettings = null;
    
    private $_sitePathNestingLimit = 10;
    
    /**
     * �����������. �������������� ��������������� �������-��������� ��� �������� ��������� ����������.
     *
     * @param array $overrides = null    ������, ���������� ����� ����� � ��������, ��� ��������������� ��������
     * ��������� � ������������ ���������������� �������, 
     * ������� ����� �� ��������� ����������� ��� ��������� ����� �����, 
     * ��� ������������ ����������� ��� �� ���������������.
     */
    public function __construct(array $overrides = null)
    {
        if (!is_null($overrides)) {
            foreach ($overrides as $name => $value) {
                $this->$name = $value;
            }
        }
        if (DIRECTORY_SEPARATOR != $this->_siteRoot) {
            $this->_cacheFsDir = $this->_siteRoot . $this->_cacheFsDir;
        } 
        if (class_exists('UfoDbSettings')) {
            $this->_dbSettings = new UfoDbSettings($this->_dbHost, 
                                                   $this->_dbLogin, 
                                                   $this->_dbPassword, 
                                                   $this->_dbName, 
                                                   $this->_dbTablePrefix);
        }
        if (class_exists('UfoCacheFsSettings')) {
            $this->_cacheFsSettings = new UfoCacheFsSettings($this->_cacheLifetime, 
                                                             $this->_cacheFsDir, 
                                                             $this->_cacheFsFileExt, 
                                                             $this->_cacheFsSavetime);
        }
    }
    
    /**
     * ��������� ������ ��� ������ � ������ ������.
     *
     * @param string $name    ��� ��������, �������� �������� ����� ��������
     * @return mixed
     */
    public function __get($name)
    {
        $arr = get_object_vars($this);
        if (array_key_exists('_' . $name, $arr)) {
            return $arr['_' . $name];
        }
        throw new Exception('Property not exists ' . $name . '.');
    }
    /**
     * ��������� ������ ��� ������ � ������ ������.
     *
     * @param string $name     ��� ��������, �������� �������� ����� ��������
     * @param mixed  $value    ����� �������� ��������
     * @return mixed           ������ �������� ��������
     */
    public function __set($name, $value)
    {
        $arr = get_object_vars($this);
        $name = '_' . $name;
        if (array_key_exists($name, $arr)) {
            $old = $arr[$name];
            $this->$name = $value;
            return $old;
        }
        throw new Exception('Property not exists ' . $name . '.');
    }
}
