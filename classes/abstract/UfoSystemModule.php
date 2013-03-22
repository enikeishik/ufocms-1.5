<?php
require_once 'UfoModule.php';
require_once 'classes/UfoToolsExt.php';
require_once 'classes/exceptions/UfoExceptionPathNotexists.php';
/**
 * Абрстрактный класс служебного модуля, дочерние классы должны реализовывать интерфейс UfoModuleInterface или быть абстрактными.
 * Все классы служебных модулей должны наследовать этот класс.
 * 
 * @author enikeishik
 *
 */
abstract class UfoSystemModule extends UfoModule
{
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
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
