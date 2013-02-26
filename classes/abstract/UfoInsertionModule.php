<?php
require_once 'UfoInsertionModuleInterface.php';
/**
 * ������������ ����� ������� ������, 
 * �������� ������ ������ ������������� 
 * ��������� UfoInsertionModuleInterface ��� ���� ������������.
 * ��� ������ ������� ������� ������ ����������� ���� �����.
 * 
 * @author enikeishik
 *
 */
abstract class UfoInsertionModule implements UfoInsertionModuleInterface
{
    use UfoTools;
    
    /**
     * ������ �� ������ ������� ������.
     * @var UfoInsertionTemplate
     */
    protected $template = null;
    
    /**
     * �����������.
     */
    public function __construct()
    {
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName();
    }
    
    /**
     * ��������� ����������� ����� �������.
     * ���� ����� ��������� ��������� ���������� �������, 
     * ������������ ��� ������ �������� (targetId) � ������� ����� (placeId).
     * @param UfoInsertionStruct $insertion    ��������� �������
     * @param int $offset = 0                  �������� �������� ������� � $offset
     * @param int $limit = 0                   �������� ����� $limit ��������� (���� $limit > 0)
     * @param array $options = null            �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generate(UfoInsertionStruct $insertion, $offset = 0, $limit = 0, array $options = null)
    {
        /*
         * 1. ���������� ������ ����� (ShowInsertions_Begin).
         * 2. ������ ������� ���������, ������� ������ ����������
         * �� ���� �������� � ���� ����� 
         * (TargetId=... OR TargetId=0) AND PlaceId=...
         * �������� ����� ��������� ����� �������.
         * 3. ��� ������ �������� ��������� $this->generateItem(...) (ShowInsertions_Item)
         * 2.-3. ���� ��������� ���, ���������� �������� ����������. (��� �������)
         * 4. ���������� ����� ����� (ShowInsertions_End).
         */
        ob_start();
        $this->template->drawBegin($options);
        echo $this->generateItem(0, $options);
        echo $this->generateItem(1, $options);
        echo $this->generateItem(2, $options);
        $this->template->drawEnd($options);
        return ob_get_clean();
    }
    
    /**
     * ��������� ����������� �������� ����� �������.
     * @param mixed $item              ������������� ��� ������ ��������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    abstract public function generateItem($item, array $options = null);
}