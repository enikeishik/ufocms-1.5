<?php
require_once 'classes/abstract/UfoInsertionItemTemplate.php';
/**
 * Класс шаблона вставок модуля документов.
 * 
 * @author enikeishik
 *
 */
class UfoTplDocumentsIns extends UfoInsertionItemTemplate
{
    /**
     * Вывод начала элемента вставки.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemBegin(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        
    }
    
    /**
     * Вывод содержимого элемента вставки.
     * Этот метод может вызываться множество раз в цикле для вывода данных элементов списков (например, новости ленты новостей).
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $data                           данные (элемента) элемента блока вставки (строка выборки из БД)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemContent(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $data, array $options = null)
    {
        if (0 < strlen($insertion->Title)) {
            echo '<div class="insdocumentstitle">' . $insertion->Title . '</div>' . "\r\n";
        }
        if (strlen($data['body']) > $insertion->ItemsLength) {
            echo '<div class="insdocumentstext">' . 
                 $this->cutNice($data['body'], $insertion->ItemsLength) . 
                 '</div>' . "\r\n" . 
                 '<div class="insdocumentsmore"><a href="' . $settings->path . '">Подробнее...</a></div>' . "\r\n";
        } else {
            echo '<div class="insdocumentstext">' . $data['body'] . '</div>' . "\r\n";
        }
    }
    
    /**
     * Вывод окончания элемента вставки.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEnd(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        
    }
    
    /**
     * Вывод заглушки, если элемент вставки не содержит инфрмации.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEmpty(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        echo '<div>Нет данных по запросу.</div>' . "\r\n";
    }
}
