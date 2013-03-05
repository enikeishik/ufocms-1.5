<?php
/**
 * ����� �������.
 * 
 * @author enikeishik
 *
 */
class UfoInsertion
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
     * ������ ��� ������ ������� ������.
     * @var UfoDbModel
     */
    private $dbModel = null;
    
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
        
        $this->loadTemplate('UfoInsertionTemplateGlobal');
        $this->template = new UfoInsertionTemplateGlobal();
    }

    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->db =& $this->container->getDb();
        $this->dbModel =& $this->container->getDbModel();
        $this->debug =& $this->container->getDebug();
    }
    
    /**
     * ��������� ����������� ����� �������.
     * ���� ����� ��������� ��������� ���������� �������, 
     * ������������ ��� ������ �������� (targetId) � ������� ����� (placeId).
     * @param int $targetId            ������������� ������� � ������� ��������� �������
     * @param int $placeId             ������������� ����� � ������� ��������� �������
     * @param int $offset = 0          �������� �������� ������� � $offset
     * @param int $limit = 0           �������� ����� $limit ��������� (���� $limit > 0)
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generate($targetId, $placeId, $offset = 0, $limit = 0, array $options = null)
    {
        $this->loadClass('UfoInsertionItemStruct');
        $this->loadClass('UfoInsertionItemSettings');
        $items = $this->dbModel->getInsertionItems($targetId, $placeId, $offset, $limit);
        ob_start();
        if (is_array($items) && 0 < count($items)) {
            $this->template->drawBegin($options);
            foreach ($items as $item) {
                echo $this->generateItem($item[1], $item[0], $options);
            }
            $this->template->drawEnd($options);
        } else {
            $this->template->drawEmpty($options);
        }
        return ob_get_clean();
    }
    
    /**
     * ��������� ����������� �������� ����� �������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ �������� ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, 
                                 UfoInsertionItemSettings $settings, 
                                 array $options = null)
    {
        //����������� �� ������� ������� 'ins_news.php' � ������ 'UfoModNews';
        $mod = $settings->mfileins;
        $mod = substr($mod, strpos($mod, '_') + 1);
        $mod = $this->config->modulesPrefix . 
               ucfirst(substr($mod, 0, strpos($mod, '.')));
        $ins = $mod . $this->config->modulesInsetionsSuffix;
        $insSet = $ins . $this->config->structSettingsSuffix;
        
        $this->loadInsertionModule($mod, $ins);
        $this->loadInsertionModule($mod, $insSet);
        $insObj = new $ins($this->container);
        $insObjSet = new $insSet($settings);
        return $insObj->generateItem($insertion, $insObjSet, $options);
    }
}
