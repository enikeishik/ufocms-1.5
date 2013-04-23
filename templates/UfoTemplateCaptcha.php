<?php
/**
 * Класс содержит методы оформления вывода CAPTCHA.
 * Данный класс используется непосредственно классом UfoCaptcha.
 * 
 * @author enikeishik
 *
 */
class UfoTemplateCaptcha
{
    /**
     * Ссылка на объект CAPTCHA.
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
