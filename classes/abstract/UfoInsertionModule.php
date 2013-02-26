<?php
require_once 'UfoInsertionModuleInterface.php';
/**
 * Абрстрактный класс вставки модуля, 
 * дочерние классы должны реализовывать 
 * интерфейс UfoInsertionModuleInterface или быть абстрактными.
 * Все классы вставок модулей должны наследовать этот класс.
 * 
 * @author enikeishik
 *
 */
abstract class UfoInsertionModule implements UfoInsertionModuleInterface
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
        
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName();
    }

    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->db =& $this->container->getDb();
        $this->debug =& $this->container->getDebug();
    }
    
    /**
     * Генерация содержимого блока вставки.
     * Блок может содержать множество конкретных вставок, 
     * определенных для данной страницы (targetId) и данного места (placeId).
     * @param UfoInsertionStruct $insertion    параметры вставки
     * @param int $offset = 0                  выводить элементы начиная с $offset
     * @param int $limit = 0                   выводить всего $limit элементов (если $limit > 0)
     * @param array $options = null            дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     */
    public function generate(UfoInsertionStruct $insertion, $offset = 0, $limit = 0, array $options = null)
    {
        /*
         * 1. Отображаем начало блока (ShowInsertions_Begin).
         * 2. Делаем выборку элементов, которые должны выводиться
         * на этой странице в этом месте 
         * (TargetId=... OR TargetId=0) AND PlaceId=...
         * получаем набор элементов блока вставки.
         * 3. Для кадого элемента запускаем $this->generateItem(...) (ShowInsertions_Item)
         * 2.-3. Если элементов нет, отображаем заданную информацию. (нет аналога)
         * 4. Отображаем конец блока (ShowInsertions_End).
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
     * Генерация содержимого элемента блока вставки.
     * @param mixed $item              идентификатор или данные элемента
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     */
    abstract public function generateItem($item, array $options = null);
}