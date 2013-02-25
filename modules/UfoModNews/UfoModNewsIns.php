<?php
require_once 'classes/abstract/UfoInsertionModule.php';

/**
 * 
 * @author enikeishik
 *
 */
class UfoModNewsIns extends UfoInsertionModule
{
    /**
     * Генерация содержимого элемента блока вставки.
     * @param mixed $item              идентификатор или данные элемента
     * @param mixed $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     */
    public function generateItem($item, array $options = null)
    {
        /*
         * 
         */
        return 'NewsInsertionItem';
    }
}