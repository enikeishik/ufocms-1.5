<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * �����-��������� ��� �������� ������ ������.
 * 
 * @author enikeishik
 *
 */
class UfoErrorStruct extends UfoStruct
{
    /**
     * Error code.
     * @var int
     */
    public $code = 0;
    
    /**
     * Error text.
     * @var string
     */
    public $text = '';
    
    /**
     * ���� ������������� ������� ��������.
     * @var string
     */
    public $pathRedirect = '';
    
    /**
     * �����������.
     * @param int $code       ��� ������
     * @param string $text    ����� ������
     */
    public function __construct($code, $text, $pathRedirect = '')
    {
        $this->code = $code;
        $this->text = $text;
        $this->pathRedirect = $pathRedirect;
    }
}
