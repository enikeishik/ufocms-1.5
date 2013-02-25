<?php
/**
 * Интерфейс модуля, обслуживающего раздел.
 * Все классы модулей должны реализовывать этот интерфейс 
 * или его дочерние интерфейсы.
 * 
 * @author enikeishik
 *
 */
interface UfoModuleInterface
{
    /**
     * Генерация основного содержимого страницы.
     * @return string
     */
    public function getPage();
}
