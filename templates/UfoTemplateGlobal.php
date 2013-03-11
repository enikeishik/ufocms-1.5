<?php
require_once 'classes/abstract/UfoTemplate.php';
/**
 * Класс содержит базовые методы оформления вывода.
 * Все классы шаблонов модулей могут наследовать этот класс.
 * Методы класса могут быть переопределены в дочерних классах для реализации специфического вывода.
 * 
 * @author enikeishik
 *
 */
abstract class UfoTemplateGlobal extends UfoTemplate
{
    public function drawHttpHeaders()
    {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time() + $this->config->httpLastModified) . ' GMT');
        header('Cache-Control: max-age=' . $this->config->httpMaxAge);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $this->config->httpExpires) . ' GMT');
        if ($this->module->getParam('rss')) {
            header('Content-type: text/xml; charset=' . $this->config->httpCharset);
        } else {
            header('Content-type: text/html; charset=' . $this->config->httpCharset);
        }
    }
    
    /**
     * Вывод заголовка, отображаемого в заголовке документа.
     */
    public function drawHeadTitle()
    {
        echo '<title>' . $this->sectionFields->title . '</title>' . "\r\n";
    }
    
    /**
     * Вывод мета тэгов.
     */
    public function drawMetaTags()
    {
    
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
        echo '<h1>' . $this->sectionFields->title . '</h1>' . "\r\n";
    }
    
    /**
     * Вывод вставки информации из разделов.
     * @param array $options = null    параметры вставки, дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawInsertion(array $options = null)
    {
        echo $this->core->insertion($options);
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
