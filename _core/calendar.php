<?php
/**
 * Класс календаря.
 
 * Класс выдает на запрошенный день имеющиеся в хранилище события.
 */

class UfoCalendar
{
    /**
     * Путь к XML файлу-хранилищу данных.
     *
     * @var string
     */
    protected $storage = '';
    
    
    
    /**
     * Конструктор.
     *
     * @param string $storage   путь к XML файлу-хранилищу данных
     * @param string $baseUrl   базовый URL
     */
    public function __construct($storage)
    {
        $this->storage = $storage;
    }
    
    public function getAllEvents()
    {
        return $this->walkThroughElements(function ($item, $params) {
                return true;
            });
    }
    
    public function getDayEvents($day, $month)
    {
        return $this->walkThroughElements(function ($item, $params) {
                return ($params['Day'] == substr($item['date'], 8, 2) 
                        && $params['Month'] == substr($item['date'], 5, 2));
            }, array('Day' => $day, 'Month' => $month));
    }
    
    public function getEvent($id)
    {
        return $this->walkThroughElements(function ($item, $params) {
                return ($params['Id'] == $item['id']);
            }, array('Id' => $id), true);
    }
    
    protected function walkThroughElements($callback, 
                                           $cbParams = array(), 
                                           $single = false)
    {
        $dom = new DOMDocument;
        if (!$dom->load($this->storage)) {
            return false;
        }
        if (false === $items = $dom->getElementsByTagName('item')) {
            return false;
        }
        
        $arr = array();
        for ($i = 0; $i < $items->length; $i++) {
            $child = $items->Item($i)->firstChild;
            $item = array();
            while ($child) {
                $val = $child->nodeValue;
                //XML в PHP всегда идет в UTF-8, поэтому перекодируем
                if (function_exists('mb_convert_variables')) {
                    mb_convert_variables('windows-1251', 'UTF-8', $val);
                } else if (function_exists('iconv')) {
                    $val = iconv('UTF-8', 'windows-1251', $val);
                } else {
                    return false;
                }
                $item[$child->nodeName] = $val;
                $child = $child->nextSibling;
            }
            if ($callback($item, $cbParams)) {
                $arr[] = $item;
                if ($single) {
                    return $arr;
                }
            }
        }
        if (!$single) {
            return $arr;
        } else {
            return false;
        }
    }
}
