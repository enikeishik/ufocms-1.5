<?php
require_once 'classes/abstract/UfoInsertionItemTemplate.php';
/**
 * ����� ������� ������� ������ ����������.
 * 
 * @author enikeishik
 *
 */
class UfoTplDocumentsIns extends UfoInsertionItemTemplate
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
        if (0 < strlen($insertion->Title)) {
            echo '<div class="insdocumentstitle">' . $insertion->Title . '</div>' . "\r\n";
        }
        if (strlen($item['body']) > $insertion->ItemsLength) {
            echo '<div class="insdocumentstext">' . 
                 $this->cutNice($item['body'], $insertion->ItemsLength) . 
                 '</div>' . "\r\n" . 
                 '<div class="insdocumentsmore"><a href="' . $settings->path . '">���������...</a></div>' . "\r\n";
        } else {
            echo '<div class="insdocumentstext">' . $item['body'] . '</div>' . "\r\n";
        }
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
