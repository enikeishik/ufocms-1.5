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
     * ������ �� ������-��������� ������ �� �������.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * ������ �� ������ ������������.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * ������ �� ������ ��� ������ � ����� ������.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * ������ �� ������ ������� ������.
     * @var UfoInsertionTemplate
     */
    protected $template = null;
    
    /**
     * �����������.
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName();
    }

    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->db =& $this->container->getDb();
        $this->debug =& $this->container->getDebug();
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
        $sql = 'SELECT Id,TargetId,PlaceId,OrderId,SourceId,SourcesIds,Title,' .
               'ItemsIds,ItemsStart,ItemsCount,ItemsLength,ItemsStartMark,ItemsStopMark,ItemsOptions' .
               ' FROM ' . $this->db->getTablePrefix() . 'insertions' .
               ' WHERE (TargetId=' . $insertion->targetId . ' OR TargetId=0)' .
               ' AND PlaceId=' . $insertion->placeId .
               ' ORDER BY OrderId';
        if (0 !=$offset && 0 != $limit) {
            $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        } else if (0 != $limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
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