<?php
/**
 * Интерфейс шаблона модуля, обслуживающего раздел.
 * Все классы шаблонов модулей должны реализовывать этот интерфейс 
 * или его дочерние интерфейсы.
 */
interface UfoTemplateInterface
{
    /**
     * Получение мета тэгов.
     * @return string
     */
    public function getMetaTags();
    
    /**
     * Получение заголовка, отображаемого в тэге <title>.
     * @return string
     */
    public function getHeadTitle();
    
    /**
     * Получение заголовка, отображаемого на странице.
     * @return string
     */
    public function getBodyTitle();
    
    /**
     * Получение основного содержимого страницы.
     * @return string
     */
    public function getBodyContent();
}
