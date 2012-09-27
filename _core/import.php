<?php
/**
 * ����������� ����� ������� ������ � ������� XML � ���������� �����.
 */
abstract class UfoImport
{
    /**
     * ����� �������� � ������� ��-���������.
     */
    const DEFAULT_URL = '';
    
    /**
     * URL �������� �������� ������ �� �����.
     *
     * @var string
     */
    protected $url = self::DEFAULT_URL;
    
    /**
     * ��������� ������ ����.
     *
     * @var UfoCache
     */
    protected $cache = null;
    
    /**
     * ����� �������� �������� ������ � ���������� �����.
     *
     * @var int
     */
    protected $socketTimeout = 5;
    
    /**
     * �����������.
     *
     * @param UfoCache &$cache                  ������ �� ��������� ������ ����
     * @param string   $url = null              URL �������� �������� ������
     * @param int      $socketTimeout = null    ������� �������� ����������
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
     * ���������� ������ ����������� ������ XML �� ������ ����������.
     *
     * @return array | false
     */
    abstract protected function parseXml(&$dom);
}
