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
     * @param UfoInsertionItemStruct $insertion    ������ �������� �������
     * @param string $path                         ���� �������-��������� �������
     * @param array $options = null                �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, $path, array $options = null)
    {
        //for backward compatibility
        $item = array_merge((array) $insertion, array('Path' => $path));
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