<?php
require_once 'UfoModule.php';
require_once 'classes/UfoToolsExt.php';
require_once 'classes/exceptions/UfoExceptionPathNotexists.php';
/**
 * ������������ ����� ���������� ������, �������� ������ ������ ������������� ��������� UfoModuleInterface ��� ���� ������������.
 * ��� ������ ��������� ������� ������ ����������� ���� �����.
 * 
 * @author enikeishik
 *
 */
abstract class UfoSystemModule extends UfoModule
{
    
    /**
     * �����������.
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     * @throws UfoExceptionPathNotexists
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        if (!is_null($this->section)) {
            $this->sectionFields = $this->section->getFields();
        }
        
        $this->parseParams();
        
        $this->container->setModule($this);
        
        $templateName = str_replace($this->config->sysmodsPrefix, 
                                    $this->config->systplsPrefix, 
                                    get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName($this->container);
    }
}
