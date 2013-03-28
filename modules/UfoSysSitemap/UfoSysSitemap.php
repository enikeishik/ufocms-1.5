<?php
require_once 'classes/abstract/UfoSystemModule.php';
/**
 * ��������� ������ ����� �����.
 * 
 * @author enikeishik
 *
 */
class UfoSysSitemap extends UfoSystemModule
{
    /**
     * ������ ��� ������ � ������� ������.
     * @var UfoSysSitemapDbModel
     */
    protected $dbModel = null;
    
    /**
     * �����������.
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     * @throws UfoExceptionPathNotexists
     */
    public function __construct(UfoContainer &$container)
    {
        parent::__construct($container);
        $module = get_class($this);
        $dbModel = $module . $this->config->dbModelSuffix;
        $this->loadModuleDbModel($module, $dbModel);
        $this->dbModel = new $dbModel($this->db);
    }
    
    /**
     * ��������� ����������� ������.
     * @param string $sortField           ����, �� �������� ����� ������������� ������
     * @param string $sortDesc = false    �������� ����������
     */
    public function getContent($sortField = 'mask', $sortDesc = false)
    {
        if ('mask' != $sortField && 'path' != $sortField) {
            $sortField = 'mask';
        }
        return $this->dbModel->getContent($sortField, $sortDesc);
    }
}
