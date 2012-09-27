<?php
require_once 'import.php';

/**
 * Класс импорта курса валют ЦБ РФ.
 */
class UfoImportCbr extends UfoImport
{
    /**
     * Адрес страницы с данными по-умолчанию.
     */
    const DEFAULT_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    
    /**
     * @return array | false
     */
    protected function parseXml(&$dom)
    {
        $arr = array();
        $xitems = $dom->getElementsByTagName('Valute');
        for ($i = 0; $i < $xitems->length; $i++) {
            $xitem = $xitems->item($i);
            if (1 == $xitem->nodeType) {
                $childs = $xitem->childNodes;
                $item = array();
                for ($j = 0; $j < $childs->length; $j++) {
                    $child = $childs->item($j);
                    if (1 == $child->nodeType) {
                        $item[$child->nodeName] = $child->nodeValue;
                    }
                }
                $arr[$xitem->getAttribute('ID')] = $item;
            }
        }
        return $arr;
    }
}
