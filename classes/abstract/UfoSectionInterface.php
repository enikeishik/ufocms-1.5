<?php
/**
 * Интерфейс раздела сайта.
 * Все классы разделов должны реализовывать этот интерфейс или его дочерние интерфейсы.
 * 
 * @author enikeishik
 *
 */
interface UfoSectionInterface
{
    /**
     * Инициализация объекта модуля, обслуживающего раздел.
     * @throws Exception
     */
    public function initModule();
    
    /**
     * Возвращение сгенерированной модулем страницы.
     * @return string
     */
    public function getPage();
}
