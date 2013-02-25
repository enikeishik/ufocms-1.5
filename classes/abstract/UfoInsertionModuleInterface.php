<?php
/**
 * ��������� ������� ������.
 * ��� ������ ������� ������� ������ ������������� ���� ��������� 
 * ��� ��� �������� ����������.
 * 
 * @author enikeishik
 *
 */
interface UfoInsertionModuleInterface
{
    /**
     * ��������� ����������� ����� �������.
     * ���� ����� ��������� ��������� ���������� �������, 
     * ������������ ��� ������ �������� (targetId) � ������� ����� (placeId).
     * @param UfoInsertionStruct $insertion    ��������� �������
     * @param int $offset = 0                  �������� �������� ������� � $offset
     * @param int $limit = 0                   �������� ����� $limit ��������� (���� $limit > 0)
     * @return string
     */
    public function generate(UfoInsertionStruct $insertion, $offset = 0, $limit = 0, array $options = null);
    
    /**
     * ��������� ����������� �������� ����� �������.
     * @param mixed $item              ������������� ��� ������ ��������
     * @param mixed $options = null    �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generateItem($item, array $options = null);
}
