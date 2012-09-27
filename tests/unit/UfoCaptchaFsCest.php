<?php

class UfoCaptchaFsCest
{
    public $class = 'UfoCaptchaFs';
    
    private $tempPath = 'c:\tmp';
    
    /**
     * ��������� ���������� ������ ������������� ������ 
     * � ������� CAPTCHA.
     * ������� ����� ������������ �������� ���� �� ������ ��������.
     */
    public function getCaptcha(\CodeGuy $I) {
        $I->wantTo('execute method `getCaptcha`');
        $captcha = new UfoCaptchaFs($this->tempPath);
        $I->executeMethod($captcha, 'getCaptcha');
        $I->seeMethodNotReturns($captcha, 'getCaptcha', array());
    }
    
    /**
     * ��������� ���������� ������ ������������� ����������� CAPTCHA.
     */
    public function showImage(\CodeGuy $I) {
        $I->wantTo('execute method `showImage`');
        $captcha = new UfoCaptchaFs($this->tempPath);
        $I->executeMethod($captcha, 'showImage');
    }
    
    /**
     * ��������� ���������� ������ ������������ ��������� ����������� 
     * �������� CAPTCHA, ��� �������� ������������ ��������, 
     * ����� ������ ������� false.
     */
    public function checkIncorrect(\CodeGuy $I) {
        $I->wantTo('execute method `check`');
        $captcha = new UfoCaptchaFs($this->tempPath, null, 'CKey', 'CVal');
        $cData = $captcha->getCaptcha();
        $_POST['CKey'] = $cData['CaptchaKey'];
        $_POST['CVal'] = '0000';
        $I->executeMethod($captcha, 'check');
        $I->wantTo('check incorrect value');
        $I->seeMethodReturns($captcha, 'check', false);
    }
    
    public function checkCorrect(\CodeGuy $I) {
        $I->wantTo('execute method `check`');
        $captcha = new UfoCaptchaFs($this->tempPath, null, 'CKey', 'CVal');
        $cData = $captcha->getCaptcha(true);
        $_POST['CKey'] = $cData['CaptchaKey'];
        $_POST['CVal'] = $cData['CaptchaValue'];
        $I->executeMethod($captcha, 'check');
        $I->wantTo('check correct value');
        $I->seeMethodReturns($captcha, 'check', true);
    }
}
