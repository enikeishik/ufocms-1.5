<?php

class UfoCaptchaFsCest
{
    public $class = 'UfoCaptchaFs';
    
    private $tempPath = 'c:\tmp';
    
    /**
     * Проверяем выполнение метода возвращающего массив 
     * с данными CAPTCHA.
     * Смотрим чтобы возвращаемое значение было не пустым массивом.
     */
    public function getCaptcha(\CodeGuy $I) {
        $I->wantTo('execute method `getCaptcha`');
        $captcha = new UfoCaptchaFs($this->tempPath);
        $I->executeMethod($captcha, 'getCaptcha');
        $I->seeMethodNotReturns($captcha, 'getCaptcha', array());
    }
    
    /**
     * Проверяем выполнение метода генерирующего изображение CAPTCHA.
     */
    public function showImage(\CodeGuy $I) {
        $I->wantTo('execute method `showImage`');
        $captcha = new UfoCaptchaFs($this->tempPath);
        $I->executeMethod($captcha, 'showImage');
    }
    
    /**
     * Проверяем выполнение метода проверяющего введенное посетителем 
     * значение CAPTCHA, при заведомо неправильном значении, 
     * метод должен вернуть false.
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
