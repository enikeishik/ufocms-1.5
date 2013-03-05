<?php
/**
 * Класс вставок.
 * 
 * @author enikeishik
 *
 */
class UfoInsertion
{
    use UfoTools;
    
    /**
     * Ссылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * Ссылка на объект конфигурации.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * Ссылка на объект для работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * Объект для работы моделью данных.
     * @var UfoDbModel
     */
    private $dbModel = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Ссылка на объект шаблона модуля.
     * @var UfoInsertionTemplate
     */
    protected $template = null;
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-контейнер ссылок на объекты
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        $this->loadTemplate('UfoInsertionTemplateGlobal');
        $this->template = new UfoInsertionTemplateGlobal();
    }

    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->db =& $this->container->getDb();
        $this->dbModel =& $this->container->getDbModel();
        $this->debug =& $this->container->getDebug();
    }
    
    /**
     * Генерация содержимого блока вставки.
     * Блок может содержать множество конкретных вставок, 
     * определенных для данной страницы (targetId) и данного места (placeId).
     * @param int $targetId            идентификатор раздела в котором выводится вставка
     * @param int $placeId             идентификатор места в котором выводится вставка
     * @param int $offset = 0          выводить элементы начиная с $offset
     * @param int $limit = 0           выводить всего $limit элементов (если $limit > 0)
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
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
     * Генерация содержимого элемента блока вставки.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные элемента вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, 
                                 UfoInsertionItemSettings $settings, 
                                 array $options = null)
    {
        //преобразуем от старого формата 'ins_news.php' к новому 'UfoModNews';
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
