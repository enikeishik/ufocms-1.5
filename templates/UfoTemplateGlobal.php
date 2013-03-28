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
        if ($this->config->debugDisplay) {
            echo '<pre>' . "\r\n";
            $debug = $this->debug->getBuffer();
            foreach ($debug as $debugData) {
                $data = (array) $debugData;
                foreach ($data as $key => $val) {
                    echo $key . ': ' . $val . "\r\n"; 
                }
                echo "\r\n";
            }
            echo '</pre>' . "\r\n";
            echo '<p>Script time: ' . $this->debug->getScriptExecutionTime() . 
                 '; SQL queries counter: ' . $this->debug->getDbQueriesCounter() . 
                 '; memory max usage: ' . $this->debug->getMemoryUsedMax() . 
                 ' (' . $this->debug->getMemoryUsedTotalMax() . ')</p>' . "\r\n";
            if (null != $ds = $this->debug->getBlockMaxExecutionTime()) {
                echo '<p>Block with max execition time: ' . $ds . '</p>' . "\r\n";
            }
        }
        echo '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->' . "\r\n";
    }
}
