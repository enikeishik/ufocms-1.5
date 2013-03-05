<?php
require_once 'classes/abstract/UfoInsertionItemTemplate.php';
/**
 * ����� ������� ������� ������ ��������.
 * 
 * @author enikeishik
 *
 */
class UfoTplNewsIns extends UfoInsertionItemTemplate
{
    /**
     * ����� ������ �������� �������.
     * �������� $settings ��� ���������� ����� ��� UfoMod*InsSettings � �������� ��� ���� ��������� ��������������� ���������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemBegin(UfoInsertionItemStruct $insertion, 
                                  UfoInsertionItemSettings $settings, 
                                  array $options = null)
    {
        if (0 < strlen($insertion->Title)) {
            echo '<div class="insnewstitle">' . $insertion->Title . '</div>' . "\r\n";
        }
    }
    
    /**
     * ����� ����������� �������� �������.
     * ���� ����� ����� ���������� ��������� ��� � ����� ��� ������ ������ ��������� ������� (��������, ������� ����� ��������).
     * �������� $settings ��� ���������� ����� ��� UfoMod*InsSettings � �������� ��� ���� ��������� ��������������� ���������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $item                           ������ (��������) �������� ����� ������� (������ ������� �� ��)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemContent(UfoInsertionItemStruct $insertion, 
                                    UfoInsertionItemSettings $settings, 
                                    array $item, 
                                    array $options = null)
    {
        echo '<div class="insnewstitle"><a href="' . $settings->path . $item['Id'] . '/">' .
                $item['Title'] . '</a></div>' . "\r\n";
        echo '<div class="insnewstext">' . $item['Announce'] . '</div>' . "\r\n";
        echo '<div class="insnewsdate">' . $item['DateCreate'] . '</div>' . "\r\n";
        echo '<div class="insnewsdivider"></div>' . "\r\n";
    }
    
    /**
     * ����� ��������� �������� �������.
     * �������� $settings ��� ���������� ����� ��� UfoMod*InsSettings � �������� ��� ���� ��������� ��������������� ���������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEnd(UfoInsertionItemStruct $insertion, 
                                UfoInsertionItemSettings $settings, 
                                array $options = null)
    {
        if (0 < strlen($insertion->Title)) {
            echo '<div class="insnewsall"><a href="' . $settings->path . '">��� ���������...</a></div>' . "\r\n";
        }
    }
    
    /**
     * ����� ��������, ���� ������� ������� �� �������� ���������.
     * �������� $settings ��� ���������� ����� ��� UfoMod*InsSettings � �������� ��� ���� ��������� ��������������� ���������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEmpty(UfoInsertionItemStruct $insertion, 
                                  UfoInsertionItemSettings $settings, 
                                  array $options = null)
    {
        echo '<div>��� ������ �� �������.</div>' . "\r\n";
    }
}
