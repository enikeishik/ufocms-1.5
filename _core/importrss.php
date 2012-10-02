<?php
require_once 'import.php';

/**
 * ����� ������� RSS.
 */
class UfoImportRss extends UfoImport
{
    /**
     * ����� �������� � ������� ��-���������.
     */
    const DEFAULT_URL = '';
    
    /**
     * �����������. �������������, �������� $url ����������.
     *
     * @param UfoCache &$cache                  ������ �� ��������� ������ ����
     * @param string   $url                     URL �������� �������� ������
     * @param int      $socketTimeout = null    ������� �������� ����������
     */
    public function __construct(UfoCache &$cache, 
                                $url, 
                                $socketTimeout = null)
    {
        parent::__construct($cache, $url, $socketTimeout);
    }
    
    /**
     * @return array | false
     */
    protected function parseXml(&$dom)
    {
        $arr = array();
        $xitems = $dom->getElementsByTagName('item');
        for ($i = 0; $i < $xitems->length; $i++) {
            $child = $xitems->Item($i)->firstChild;
            $item = array();
            while ($child) {
                $val = $child->nodeValue;
                //XML � PHP ������ ���� � UTF-8, ������� ������������
                if (function_exists('mb_convert_variables')) {
                    mb_convert_variables('windows-1251', 'UTF-8', $val);
                } else if (function_exists('iconv')) {
                    $val = iconv('UTF-8', 'windows-1251//TRANSLIT', $val);
                } else {
                    return FALSE;
                }
                if ('enclosure' != $child->nodeName) {
                    $item[$child->nodeName] = $val;
                } else {
                    $item[$child->nodeName] = $child->getAttribute('url');
                }
                $child = $child->nextSibling;
            }
            $arr[] = $item;
        }
        return $arr;
    }
}
