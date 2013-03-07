<?php
require_once 'classes/abstract/UfoTemplate.php';
/**
 * Класс содержит методы оформления страниц ошибок.
 * 
 * @author enikeishik
 *
 */
class UfoTemplateError extends UfoTemplate
{
    /**
     * Вывод заголовка, отображаемого в заголовке документа.
     */
    public function drawHeadTitle()
    {
        echo '<title>' . $this->error->text . '</title>' . "\r\n";
    }
    
    /**
     * Вывод мета тэгов.
     */
    public function drawMetaTags()
    {
        echo '<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">';
    }
    
    /**
     * Вывод дополнительного кода (JS, CSS, ...) в заголовке документа.
     */
    public function drawHeadCode()
    {
        
    }
    
    /**
     * Вывод заголовка, отображаемого на странице.
     */
    public function drawBodyTitle()
    {
        echo '<h1>' . $this->error->text . '</h1>' . "\r\n";
    }
    
    /**
     * Вывод вставки информации из разделов.
     * @param array $params = null    параметры вставки, дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawInsertion(array $params = null)
    {
        
    }
    
    /**
     * Вывод информации отладки (в конце страницы, в виде комментария HTML).
     */
    public function drawDebug()
    {
        if (is_null($this->debug)) {
            return;
        }
        echo '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->' . "\r\n";
    }
}
