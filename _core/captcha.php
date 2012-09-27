<?php
/**
 * ������������ ����� CAPTCHA.
 *
 * ���� ����� ���������� �����������.
 * ����� ������� ����������� ������������ ��������� ������ � 
 * ��������������� �� ����. ���� � �������� ��������� � ���������. 
 * ����������� ������������ �� �����, ������� �� ��������� ������ 
 * � �������� �� � �����������.
 * ������� ������������ ������� ��������� �� ���������� ������.
 * ��� �������� ����������� ���� � ��������� ������������� ��������, 
 * ����� ���� ���������� ����� ����� � ��������� � ������������ 
 * �������� � ��������� � ����������������.
 */
abstract class UfoCaptcha
{
    /**
     * ��� ���������� �����, ������������ � GET ��� ������� ��������.
     *
     * @var string
     */
    protected $htmlGetFieldKey = '';
    
    /**
     * ��� ���� �����, � ������� ���������� ���� (� POST �������), 
     * ��� ������������� CAPTCHA.
     *
     * @var string
     */
    protected $htmlPostFieldKey = '';
    
    /**
     * ��� ���� �����, � ������� ���������� ������ �������� CAPTCHA.
     *
     * @var string
     */
    protected $htmlPostFieldValue = '';
    
    /**
     * ����� ����� ������.
     *
     * @var int
     */
    protected $stackLifetime = 600;
    
    
    /**
     * ��������� �������� CAPTCHA �� ����� �� ���������.
     *
     * @param string $key   ���� ��� ������� �� ���� ������
     *
     * @return string | false
     */
    abstract protected function fromStack($key);
    
    /**
     * ���������� ����� � �������� � ���������.
     *
     * @param string $key     ����
     * @param string $value   ��������
     *
     * @return boolean
     */
    abstract protected function intoStack($key, $value);
    
    /**
     * ������� ��������� �� ������ ������.
     *
     * @return void
     */
    abstract protected function clearOldStack();
    
    /**
     * ����������� �������� CAPTCHA.
     *
     * @return void
     */
    abstract public function showImage();
    
    /**
     * ���������� ������ ������ � CAPTCHA,
     * ����� �������������� ��� ���������� CAPTCHA � �������.
     *
     * @return array(GetFieldKey, PostFieldKey, PostFieldValue, Ticket)
     */
    abstract public function getCaptcha();
    
    /**
     * ��������� ��������� ����������� CAPTCHA
     *
     * @return boolean
     */
    abstract public function check();
}
