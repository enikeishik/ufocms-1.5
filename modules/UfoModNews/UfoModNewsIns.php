<?php
require_once 'classes/abstract/UfoInsertionModule.php';

/**
 * 
 * @author enikeishik
 *
 */
class UfoModNewsIns extends UfoInsertionModule
{
    /**
     * ��������� ����������� �������� ����� �������.
     * @param mixed $item              ������������� ��� ������ ��������
     * @param mixed $options = null    �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generateItem($item, array $options = null)
    {
        /*
         * 
         */
        return 'NewsInsertionItem';
    }
}