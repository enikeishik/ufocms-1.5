<?php
/**
 * ����� �������� ������ ���������� ������ CAPTCHA.
 * ������ ����� ������������ ��������������� ������� UfoCaptcha.
 * 
 * @author enikeishik
 *
 */
class UfoTemplateCaptcha
{
    /**
     * ������ �� ������ CAPTCHA.
     * @var UfoCaptcha
     */
    protected $captcha = null;
    
    public function  __construct(UfoCaptcha &$captcha)
    {
        $this->captcha =& $captcha;
    }
    
    public function drawCaptcha()
    {
        
    }
}
