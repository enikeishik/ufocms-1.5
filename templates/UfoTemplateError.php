<?php
require_once 'classes/abstract/UfoTemplate.php';
/**
 * ����� �������� ������ ���������� ������� ������.
 * 
 * @author enikeishik
 *
 */
class UfoTemplateError extends UfoTemplate
{
    /**
     * ������-��������� � ������� ������.
     * @var UfoErrorStruct
     */
    protected $errorData = null;
    
    /**
     * ����� HTTP ����������.
     */
    public function drawHttpHeaders()
    {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time() + 1000000) . ' GMT');
        header('Cache-Control: max-age=' . -1000000);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() - 1000000) . ' GMT');
        header('Content-type: text/html; charset=' . $this->config->httpCharset);
        if (301 == $this->errorData->code) {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $this->errorData->pathRedirect);
        }
    }
    
    /**
     * ����� ���������, ������������� � ��������� ���������.
     */
    public function drawHeadTitle()
    {
        //echo '<title>' . $this->errorData->code . ' ' . $this->errorData->text . '</title>' . "\r\n";
        echo '<TITLE>' . $this->errorData->code . ' ';
        switch ($this->errorData->code) {
            case 301:
                echo 'Moved Permanently';
                break;
            case 403:
                echo 'Forbidden';
                break;
            case 404:
                echo 'Not Found';
                break;
            case 500:
                echo 'Internal Server Error';
                break;
        }
        echo '</TITLE>' . "\r\n";
    }
    
    /**
     * ����� ���� �����.
     */
    public function drawMetaTags()
    {
        echo '<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">';
        if (301 == $this->errorData->code) {
            echo '<META HTTP-EQUIV="REFRESH" CONTENT="0; http://' . $_SERVER['HTTP_HOST'] . $this->errorData->pathRedirect . '"';
        }
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
        //echo '<h1>' . $this->errorData->text . '</h1>' . "\r\n";
        echo '<H1>';
        switch ($this->errorData->code) {
            case 301:
                echo 'Moved Permanently';
                break;
            case 403:
                echo 'Forbidden';
                break;
            case 404:
                echo 'Not Found';
                break;
            case 500:
                echo 'Internal Server Error';
                break;
        }
        echo '</H1>' . "\r\n";
    }
    
    /**
     * ����� ��������� ����������� ��������.
     */
    public function drawBodyContent()
    {
        switch ($this->errorData->code) {
            case 301:
                echo 'The document has moved <a href="http://' . 
                     $_SERVER['HTTP_HOST'] . $this->errorData->pathRedirect . 
                     '">here</a>.' . "\r\n";
                break;
            case 403:
                echo 'You don\'t have permission to access this page.' . "\r\n";
                break;
            case 404:
                echo 'The requested URL was not found on this server.' . "\r\n";
                break;
            case 500:
                echo 'The server encountered an internal error or' . 
                     'misconfiguration and was unable to complete' . 
                     'your request.' . "\r\n";
                break;
        }
    }
    
    /**
     * ����� ������� ���������� �� ��������.
     * @param array $options = null    ��������� �������, �������������� ������, ������������ ������ ������� �������
     */
    public function drawInsertion(array $options = null)
    {
        
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
