<?php
require_once 'classes/abstract/UfoInsertionItemTemplate.php';
/**
 * Класс шаблона вставок модуля новостей.
 * 
 * @author enikeishik
 *
 */
class UfoTplNewsIns extends UfoInsertionItemTemplate
{
    /**
     * Вывод начала элемента вставки.
     * Параметр $settings при выполнении имеет тип UfoMod*InsSettings и содержит все поля структуры соответствующей структуры.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemBegin(UfoInsertionItemStruct $insertion, 
                                  UfoInsertionItemSettings $settings, 
                                  array $options = null)
    {
        if (0 < strlen($insertion->Title)) {
            echo '<div class="insnewstitle">' . $insertion->Title . '</div>' . "\r\n";
        }
    }
    
    /**
     * Вывод содержимого элемента вставки.
     * Этот метод может вызываться множество раз в цикле для вывода данных элементов списков (например, новости ленты новостей).
     * Параметр $settings при выполнении имеет тип UfoMod*InsSettings и содержит все поля структуры соответствующей структуры.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $item                           данные (элемента) элемента блока вставки (строка выборки из БД)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemContent(UfoInsertionItemStruct $insertion, 
                                    UfoInsertionItemSettings $settings, 
                                    array $item, 
                                    array $options = null)
    {
        echo '<div class="insnewstitle"><a href="' . $settings->path . $item['Id'] . '/">' .
                $item['Title'] . '</a></div>' . "\r\n";
        echo '<div class="insnewstext">' . $item['Announce'] . '</div>' . "\r\n";
        echo '<div class="insnewsdate">' . $item['DateCreate'] . '</div>' . "\r\n";
        echo '<div class="insnewsdivider"></div>' . "\r\n";
    }
    
    /**
     * Вывод окончания элемента вставки.
     * Параметр $settings при выполнении имеет тип UfoMod*InsSettings и содержит все поля структуры соответствующей структуры.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEnd(UfoInsertionItemStruct $insertion, 
                                UfoInsertionItemSettings $settings, 
                                array $options = null)
    {
        if (0 < strlen($insertion->Title)) {
            echo '<div class="insnewsall"><a href="' . $settings->path . '">Все материалы...</a></div>' . "\r\n";
        }
    }
    
    /**
     * Вывод заглушки, если элемент вставки не содержит инфрмации.
     * Параметр $settings при выполнении имеет тип UfoMod*InsSettings и содержит все поля структуры соответствующей структуры.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEmpty(UfoInsertionItemStruct $insertion, 
                                  UfoInsertionItemSettings $settings, 
                                  array $options = null)
    {
        echo '<div>Нет данных по запросу.</div>' . "\r\n";
    }
}
