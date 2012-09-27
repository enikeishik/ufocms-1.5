<?php
require_once 'import.php';

/**
 * ����� ������� �������� ������ � ����� www.gismeteo.ru.
 */
class UfoImportGismeteo extends UfoImport
{
    /**
     * ����� �������� � ������� ��-���������.
     */
    const DEFAULT_URL = 'http://informer.gismeteo.ru/xml/27595_1.xml';
    
    /**
     * @return array | false
     */
    protected function parseXml(&$dom)
    {
        $arr = array();
        $xitems = $dom->getElementsByTagName('FORECAST');
        for ($i = 0; $i < $xitems->length; $i++) {
            $xitem = $xitems->item($i);
            if (1 == $xitem->nodeType) {
                $item = array();
                $item['FORECASTday']     = $xitem->getAttribute('day');
                $item['FORECASTmonth']   = $xitem->getAttribute('month');
                $item['FORECASTyear']    = $xitem->getAttribute('year');
                $item['FORECASThour']    = $xitem->getAttribute('hour');
                $item['FORECASTtod']     = $xitem->getAttribute('tod');
                $item['FORECASTpredict'] = $xitem->getAttribute('predict');
                $item['FORECASTweekday'] = $xitem->getAttribute('weekday');
                $childs = $xitem->childNodes;
                for ($j = 0; $j < $childs->length; $j++) {
                    $child = $childs->item($j);
                    if (1 == $child->nodeType) {
                        switch ($child->nodeName) {
                            case 'PHENOMENA':
                                $item['PHENOMENAcloudiness']    = $child->getAttribute('cloudiness');
                                $item['PHENOMENAprecipitation'] = $child->getAttribute('precipitation');
                                $item['PHENOMENArpower']        = $child->getAttribute('rpower');
                                $item['PHENOMENAspower']        = $child->getAttribute('spower');
                                break;
                            case 'PRESSURE':
                                $item['PRESSUREmax'] = $child->getAttribute('max');
                                $item['PRESSUREmin'] = $child->getAttribute('min');
                                break;
                            case 'TEMPERATURE':
                                $item['TEMPERATUREmax'] = $child->getAttribute('max');
                                $item['TEMPERATUREmin'] = $child->getAttribute('min');
                                break;
                            case 'RELWET':
                                $item['RELWETmax'] = $child->getAttribute('max');
                                $item['RELWETmin'] = $child->getAttribute('min');
                                break;
                            case 'HEAT':
                                $item['HEATmax'] = $child->getAttribute('max');
                                $item['HEATmin'] = $child->getAttribute('min');
                                break;
                            case 'WIND':
                                $item['WINDmax'] = $child->getAttribute('max');
                                $item['WINDmin'] = $child->getAttribute('min');
                                $item['WINDdirection'] = $child->getAttribute('direction');
                                break;
                        }
                    }
                }
                $arr[$xitem->getAttribute('ID')] = $item;
            }
        }
        return $arr;
    }
}
/*
�������� �������:

TOWN ���������� � ������ ���������������: 
    index ���������� ����������� ��� ������ 
    sname �������������� �������� ������ 
    latitude ������ � ����� �������� 
    longitude ������� � ����� �������� 
FORECAST ���������� � ����� ���������������: 
    day, month, year ����, �� ������� ��������� ������� � ������ ����� 
    hour ������� �����, �� ������� ��������� ������� 
    tod ����� �����, ��� �������� ��������� �������: 0 - ���� 1 - ����, 2 - ����, 3 - ����� 
    weekday ���� ������, 1 - �����������, 2 - �����������, � �.�. 
    predict ������������������ �������� � ����� 
PHENOMENA  ����������� �������: 
    cloudiness ���������� �� ���������:  0 - ����, 1- �����������, 2 - �������, 3 - �������� 
    precipitation ��� �������: 4 - �����, 5 - ������, 6,7 � ����, 8 - �����, 9 - ��� ������, 10 - ��� ������� 
    rpower ������������� �������, ���� ��� ����. 0 - �������� �����/����, 1 - �����/���� 
    spower ����������� �����, ���� ��������������: 0 - �������� �����, 1 - ����� 
PRESSURE ����������� ��������, � ��.��.��. 
    min, max
TEMPERATURE ����������� �������, � �������� ������� 
    min, max
WIND ��������� ����� 
    min, max ����������� � ������������ �������� ������� �������� �����, ��� ������� 
    direction  ����������� ����� � ������, 0 - ��������, 1 - ������-���������,  � �.�. 
RELWET ������������� ��������� �������, � % 
    min, max
HEAT ������� - ����������� ������� �� �������� ������� �� ������ ��������, ���������� �� ����� 
    min, max
*/
