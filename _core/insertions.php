<?php
class UfoInsertions
{
    /**
     * �������� �������, ���������� �� ��������
     * ����� ��������� ����� ���������� ��� ���������
     * ������ ��� �������� ���������� � �������
     * ������������ ������ � ��������.
     *
     * @param array $options = null     �������������� ���������
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
     * ������ ������� ������������ ��� ������� �������� ���������, 
     * ���������� ������� � �������������� ����������.
     * �������� $insertion ������ ������������ ����� ������ � ��������� 
     * ���������� �������:
     * array(
     * 'Title' => '��������� �������', 
     * 'ItemsIds' => '12, 13, 14, 25, 34, 49', - ������� �������� � ���������� ����������������
     * 'ItemsStart' => 0, - �������� ������� � ��������, ��� �������� ���������� ��������
     * 'ItemsCount' => 5, - �������� ���������, ��� �������� ���������� ��������
     * 'ItemsLength' => 255, - �������� ������ 255 �������� ������, ��� �������� �� ���������� ��������, ���� ��� ������� ������ ��������, ���� � �������� ��� ������, ���� �� ������� ���� �������� �������
     * 'ItemsStartMark' => '<!-- start -->', - ����� ������ ����������� �� ��������� ����� ������
     * 'ItemsStopMark' => '<!-- stop -->', - ����� ����� ����������� �� ��������� ����� ������
     * 'ItemsOptions' => '...' �������������� ���������, �������������� ���������� ������� �� ������ ����������
     * )
     * ��� �������� ������� �� �����������, �� ��� �������� � ���������� 
     * ���������� ��������� �������� � ����������, ���� ���������� ��������������.
     *
     * @param int    $id                ������������� �������-��������� �������
     * @param array  $insertion         ��������� �������
     * @param array  $options = null    �������������� ���������
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
     * ������ ������� ������������ ��� ������� �������� ���������, 
     * ���������� ������� � �������������� ����������.
     *
     * @param string $url               URL �������-��������� �������
     * @param array  $insertion         ��������� �������
     * @param array  $options = null    �������������� ���������
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
     * ����������� ������ ������� 
     * � �������� ���������� ���������������� ���� ������.
     *
     * @param array  $insertion         ��������� �������
     * @param array  $options           �������������� ���������
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
