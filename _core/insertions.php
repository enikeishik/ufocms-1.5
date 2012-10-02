<?php
class UfoInsertions
{
    /**
     * Основная функция, вызывается из шаблонов
     * может принимать набор параметров для уточнения
     * вывода или передачи параметров в функции
     * формирования вывода в шаблонах.
     *
     * @param array $options = null     дополнительные параметры
     *
     * @return void
     */
    public static function show(array $options = null)
    {
        if (!array_key_exists('PlaceId', $options)) {
            $options = array_merge($options, array('PlaceId' => 0));
        }
        
        $sql = 'SELECT Id,TargetId,PlaceId,OrderId,SourceId,SourcesIds,Title,' . 
               'ItemsIds,ItemsStart,ItemsCount,ItemsLength,ItemsStartMark,ItemsStopMark,ItemsOptions' . 
               ' FROM ' . C_DB_TABLE_PREFIX . 'insertions' . 
               ' WHERE (TargetId=' . $GLOBALS['section']['id'] . ' OR TargetId=0)' . 
                   ' AND PlaceId=' . $options['PlaceId'] . 
               ' ORDER BY OrderId';
        if (array_key_exists('Offset', $options) && array_key_exists('Limit', $options)) {
            $sql .= ' LIMIT ' . (int) $options['Offset'] . ', ' . (int) $options['Limit'];
        } else if (array_key_exists('Limit', $options)) {
            $sql .= ' LIMIT ' . (int) $options['Limit'];
        }
        $result = mysql_query($sql);
        if (!$result) {
            return;
        }
        $insertions_count = mysql_num_rows($result);
        if ($insertions_count > 0) {
            $ins_cnt = 0;
            $options = array_merge($options, 
                                   array('InsertionsCount' => $insertions_count));
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                self::showItem($row, 
                               array_merge($options, 
                                           array('InsertionNumber' => $ins_cnt)));
                $ins_cnt++;
            }
        }
        mysql_free_result($result);
    }
    
    /**
     * Данная функция используется для ручного указания источника, 
     * параметров вставки и дополнительных параметров.
     * Параметр $insertion должен представлять собой массив с указанием 
     * параметров вставки:
     * array(
     * 'Title' => 'Заголовок вставки', 
     * 'ItemsIds' => '12, 13, 14, 25, 34, 49', - выбрать элементы с указанными идентификаторами
     * 'ItemsStart' => 0, - начинать выборку с элемента, для разделов содержащих элементы
     * 'ItemsCount' => 5, - выводить элементов, для разделов содержащих элементы
     * 'ItemsLength' => 255, - выводить первые 255 символов текста, для разделов не содержащих элементы, либо для обрезки анонса элемента, если у элемента нет анонса, либо не указано иное значение обрезки
     * 'ItemsStartMark' => '<!-- start -->', - метка начала вырезаемого из источника блока текста
     * 'ItemsStopMark' => '<!-- stop -->', - метка конца вырезаемого из источника блока текста
     * 'ItemsOptions' => '...' дополнительные параметры, обрабатываемые конкретным модулем по своему усмотрению
     * )
     * все элементы массива не обязательны, но для разделов с элементами 
     * необходимо указывать смещение и количество, либо конкретные идентификаторы.
     *
     * @param int    $id                идентификатор раздела-источника вставки
     * @param array  $insertion         параметры вставки
     * @param array  $options = null    дополнительные параметры
     *
     * @return void
     */
    public static function showFrom($id, 
                                    array $insertion, 
                                    array $options = null)
    {
        if (0 == $id) {
            return;
        }
        $source = UfoCore::getSection($id);
        if (false === $source) {
            return;
        }
        $insertion['SourceId'] = $source['id'];
        if (!array_key_exists('Title', $insertion)) {
            $insertion['Title'] = '';
        }
        
        self::showItem($insertion, 
                       array_merge($options, array('InsertionsCount' => 1, 
                                                   'InsertionNumber' => 0)));
    }
    
    /**
     * Данная функция используется для ручного указания источника, 
     * параметров вставки и дополнительных параметров.
     *
     * @param string $url               URL раздела-источника вставки
     * @param array  $insertion         параметры вставки
     * @param array  $options = null    дополнительные параметры
     *
     * @return void
     */
    public static function showFromUrl($url, 
                                       array $insertion, 
                                       array $options = null)
    {
        self::showFrom(UfoCore::getIdByUrl($url), $insertion, $options);
    }
    
    /**
     * Определение модуля вставки 
     * и передача управления соответствующему коду модуля.
     *
     * @param array  $insertion         параметры вставки
     * @param array  $options           дополнительные параметры
     *
     * @return void
     */
    protected static function showItem(array $insertion, 
                                       array $options)
    {
        /*
        $mfileins = 'ins_disabled.php';
        
        $sql = 'SELECT mfileins,path FROM ' . C_DB_TABLE_PREFIX . 'modules' . 
               ' INNER JOIN ' . C_DB_TABLE_PREFIX . 'sections' . 
               ' ON ' . C_DB_TABLE_PREFIX . 'modules.muid=' . C_DB_TABLE_PREFIX . 'sections.moduleid' . 
               ' WHERE ' . C_DB_TABLE_PREFIX . 'sections.isenabled<>0' .  
               ' AND ' . C_DB_TABLE_PREFIX . 'modules.isenabled<>0' .  
               ' AND ' . C_DB_TABLE_PREFIX . 'sections.id=' . $insertion['SourceId'];
        $result = mysql_query($sql);
        if ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
            $mfileins = $row['mfileins'];
            $insertion = array_merge($insertion, array('Path' => $row['path']));
        }
        mysql_free_result($result);
        */
        
        $moduleInsetionClass = '';
        $moduleInsetionTemplateClass = '';
        
        UfoCore::loadClass('_modules,' . $moduleInsetionClass);
        UfoCore::loadClass('_templates,' . $moduleInsetionTemplateClass);
        $moduleInsetionClass::show($insertion, $options, 
                                   new $moduleInsetionTemplateClass());
    }
}

class UfoInsertionNews
{
    public static function show(array $insertion, 
                                array $options, 
                                UfoInsertionTemplate &$template)
    {
        if (...) {
            $template->begin($options);
            while (...) {
                $template->item($row, $options);
            }
            $template->end($options);
        } else {
            $template->blank($options);
        }
    }
}

interface UfoInsertionTemplate
{
    public function begin($options);
    
    public function item($data, $options);
    
    public function end($options);
    
    public function blank($options);
}

class UfoInsertionNewsTemplate implements UfoInsertionTemplate
{
    
}
