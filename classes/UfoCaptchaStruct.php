<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * �����-��������� ��� �������� ���������� ��������, ������������ CAPTCHA.
 * 
 * @author enikeishik
 *
 */
class UfoCaptchaStruct extends UfoStruct
{
    public $bgColor = array();
    public $fgColor = array();
    public $jpegQuality = 0;
    public $fontSize = 0;
    public $letterSeperator = '';
}
