<?php
require_once 'classes/abstract/UfoSystemModule.php';
/**
 * Служебный модуль Карта сайта.
 * 
 * @author enikeishik
 *
 */
class UfoSysSitemap extends UfoSystemModule
{
    /**
     * Объект для работы с моделью данных.
     * @var UfoSysSitemapDbModel
     */
    protected $dbModel = null;
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
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
     * Получение содержимого модуля.
     * @param string $sortField           поле, по которому будут отсортированы строки
     * @param string $sortDesc = false    обратная сортировка
     */
    public function getContent($sortField = 'mask', $sortDesc = false)
    {
        if ('mask' != $sortField && 'path' != $sortField) {
            $sortField = 'mask';
        }
        return $this->dbModel->getContent($sortField, $sortDesc);
    }
}
