<?php
require_once 'classes/abstract/UfoTemplate.php';
/**
 * ����� �������� ������� ������ ���������� ������.
 * ��� ������ �������� ������� ����� ����������� ���� �����.
 * ������ ������ ����� ���� �������������� � �������� ������� ��� ���������� �������������� ������.
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
     * ����� ���������, ������������� � ��������� ���������.
     */
    public function drawHeadTitle()
    {
        echo '<title>' . $this->sectionFields->title . '</title>' . "\r\n";
    }
    
    /**
     * ����� ���� �����.
     */
    public function drawMetaTags()
    {
    
    }
    
    /**
     * ����� ��������������� ���� (JS, CSS, ...) � ��������� ���������.
     */
    public function drawHeadCode()
    {
    
    }
    
    /**
     * ����� ���������, ������������� �� ��������.
     */
    public function drawBodyTitle()
    {
        echo '<h1>' . $this->sectionFields->title . '</h1>' . "\r\n";
    }
    
    /**
     * ����� ������� ���������� �� ��������.
     * @param array $options = null    ��������� �������, �������������� ������, ������������ ������ ������� �������
     */
    public function drawInsertion(array $options = null)
    {
        echo $this->core->insertion($options);
    }
    
    /**
     * ����� ���������� ������� (� ����� ��������, � ���� ����������� HTML).
     */
    public function drawDebug()
    {
        if (is_null($this->debug)) {
            return;
        }
        echo '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->' . "\r\n";
    }
}
