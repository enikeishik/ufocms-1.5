<?php
require_once 'classes/abstract/UfoInsertionModule.php';
/**
 * Класс вставки модуля новости.
 * 
 * @author enikeishik
 *
 */
class UfoModNewsIns extends UfoInsertionItemModule
{
    /**
     * Генерация содержимого элемента блока вставки.
     * @param UfoInsertionItemStruct $insertion    данные элемента вставки
     * @param string $path                         путь раздела-источника вставки
     * @param array $options = null                дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, $path, array $options = null)
    {
        //for backward compatibility
        $item = array_merge((array) $insertion, array('Path' => $path));
        ob_start();
        if (1) {
            $this->template->drawItemBegin($item, $options);
            for ($i = 0; $i < 3; $i++) {
                $data = array('i' => $i);
                $this->template->drawItemContent($item, $data, $options);
            }
            $this->template->drawItemEnd($item, $options);
        } else {
            $this->template->drawItemEmpty($item, $options);
        }
        return ob_get_clean();
    }
}