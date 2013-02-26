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
     * @param mixed $item              ������������� ��� ��������� �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generateItem($item, array $options = null)
    {
        ob_start();
        $this->template->drawItemBegin($item, $options);
        for ($i = 0; $i < 3; $i++) {
            $data = array('i' => $i);
            $this->template->drawItemContent($item, $data, $options);
        }
        $this->template->drawItemEnd($item, $options);
        return ob_get_clean();
    }
}