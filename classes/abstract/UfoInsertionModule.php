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
     * Ссылка на объект шаблона модуля.
     * @var UfoTemplateInsertion
     */
    protected $template = null;
    
    /**
     * Конструктор.
     */
    public function __construct()
    {
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName();
    }
    
    /**
     * Генерация содержимого блока вставки.
     * Блок может содержать множество конкретных вставок, 
     * определенных для данной страницы (targetId) и данного места (placeId).
     * @param UfoInsertionStruct $insertion    параметры вставки
     * @param int $offset = 0                  выводить элементы начиная с $offset
     * @param int $limit = 0                   выводить всего $limit элементов (если $limit > 0)
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
    }
}