<?php
/**
 * Абстрактный класс импорта данных в формате XML с удаленного сайта.
 */
abstract class UfoImport
{
    /**
     * Адрес страницы с данными по-умолчанию.
     */
    const DEFAULT_URL = '';
    
    /**
     * URL страницы экспорта данных на сайте.
     *
     * @var string
     */
    protected $url = self::DEFAULT_URL;
    
    /**
     * Экземпляр класса кэша.
     *
     * @var UfoCache
     */
    protected $cache = null;
    
    /**
     * Время ожидания загрузки данных с удаленного сайта.
     *
     * @var int
     */
    protected $socketTimeout = 5;
    
    /**
     * Конструктор.
     *
     * @param UfoCache &$cache                  ссылка на экземпляр класса кэша
     * @param string   $url = null              URL страницы экспорта данных
     * @param int      $socketTimeout = null    таймаут сетевого соединения
     */
    public function __construct(UfoCache &$cache, 
                                $url = null, 
                                $socketTimeout = null)
    {
        $this->cache = $cache;
        if (!is_null($url)) {
            $this->url = $url;
        }
        if (!is_null($socketTimeout)) {
            $this->socketTimeout = $socketTimeout;
        }
    }
    
    /**
     * @return array | false
     */
    public function getItems()
    {
        if ($this->cache->expired()) {
            $this->cache->save($this->loadXml());
        }
        return $this->openXml();
    }
    
    /**
     * @return string
     */
    protected function loadXml()
    {
        ini_set('allow_url_fopen', 1);
        ini_set('default_socket_timeout', $this->socketTimeout);
        return file_get_contents($this->url);
    }
    
    /**
     * @return array | false
     */
    protected function openXml()
    {
        $dom = new DOMDocument;
        if (!$dom->loadXML($this->cache->load())) {
            return false;
        }
        return $this->parseXml($dom);
    }
    
    /**
     * Наследники должны реализовать разбор XML по своему усмотрению.
     *
     * @return array | false
     */
    abstract protected function parseXml(&$dom);
}
