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
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemBegin(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        
    }
    
    /**
     * ����� ����������� �������� �������.
     * ���� ����� ����� ���������� ��������� ��� � ����� ��� ������ ������ ��������� ������� (��������, ������� ����� ��������).
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $data                           ������ (��������) �������� ����� ������� (������ ������� �� ��)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemContent(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $data, array $options = null)
    {
        if (0 < strlen($insertion->Title)) {
            echo '<div class="insdocumentstitle">' . $insertion->Title . '</div>' . "\r\n";
        }
        if (strlen($data['body']) > $insertion->ItemsLength) {
            echo '<div class="insdocumentstext">' . 
                 $this->cutNice($data['body'], $insertion->ItemsLength) . 
                 '</div>' . "\r\n" . 
                 '<div class="insdocumentsmore"><a href="' . $settings->path . '">���������...</a></div>' . "\r\n";
        } else {
            echo '<div class="insdocumentstext">' . $data['body'] . '</div>' . "\r\n";
        }
    }
    
    /**
     * ����� ��������� �������� �������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEnd(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        
    }
    
    /**
     * ����� ��������, ���� ������� ������� �� �������� ���������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEmpty(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        echo '<div>��� ������ �� �������.</div>' . "\r\n";
    }
}
