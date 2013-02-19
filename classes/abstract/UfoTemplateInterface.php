<?php
/**
 * Интерфейс шаблона модуля, обслуживающего раздел.
 * Все классы шаблонов модулей должны реализовывать этот интерфейс 
 * или его дочерние интерфейсы.
 */
interface UfoTemplateInterface
{
    /**
     * Вывод мета тэгов.
     */
    public function drawMetaTags();
    
    /**
     * Вывод заголовка, отображаемого в заголовке документа.
     */
    public function drawHeadTitle();
    
    /**
     * Вывод дополнительного кода (JS, CSS, ...) в заголовке документа.
     */
    public function drawHeadCode();
    
    /**
     * Вывод заголовка, отображаемого на странице.
     */
    public function drawBodyTitle();
    
    /**
     * Вывод основного содержимого страницы.
     */
    public function drawBodyContent();
}
