<?php
require_once 'UfoInsertionItemModuleInterface.php';
/**
 * ������������ ����� ������� ������, 
 * �������� ������ ������ ������������� 
 * ��������� UfoInsertionModuleInterface ��� ���� ������������.
 * ��� ������ ������� ������� ������ ����������� ���� �����.
 * 
 * @author enikeishik
 *
 */
abstract class UfoInsertionItemModule implements UfoInsertionItemModuleInterface
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
     * @var UfoCoreDbModel
     */
    private $coreDbModel = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * ������ �� ������ ������� ������.
     * @var UfoInsertionItemTemplate
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
        $this->coreDbModel =& $this->container->getCoreDbModel();
        $this->debug =& $this->container->getDebug();
    }
}