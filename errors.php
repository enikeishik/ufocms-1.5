<?php
/**
 * ����� ���������� ����� ��������� �������� ��������� ������.
 * 
 * @author enikeishik
 *
 */
final class UfoErrors
{
    /**
     * ������ �������� �����.
     * @var string
     */
    public $fsUnlink = 'Can not unlink file %1s';
    
    /**
     * ������ �������� �����.
     * @var string
     */
    public $fsOpenDir = 'Can not open dir %1s';
    
    /**
     * ������� ������ ���� �������.
     * @var string
     */
    public $pathEmpty = 'Path empty, main page redirect required';
    
    /**
     * ���� �������� ������������ �������.
     * @var string
     */
    public $pathBad = 'Bad path';
    
    /**
     * ���� �� �������� ������������ ����� '/'.
     * @var string
     */
    public $pathUnclosed = 'Closing slash omitted';
    
    /**
     * ���� �������� ��� �����, ������� �� ����������.
     * @var string
     */
    public $pathFileNotExists = 'Asking for a file which not exists';
    
    /**
     * ���� ������� ������� ��� �������� ������� ����� ������� �����������.
     * @var string
     */
    public $pathComplex = 'Path is too complex';
    
    /**
     * ������� � ����� ����� �� ����������.
     * @var string
     */
    public $pathNotExists = 'Path not exists';
    
    /**
     * �� ������� �������� ������ �������.
     * @var string
     */
    public $sectionFieldsUnset = 'Fields not set ($section: %1s)';
    
    /**
     * ����������� ������ (����, ������������� ��� ������).
     * @var string
     */
    public $sectionIncorrect = 'Incorrect $section: %1s';
    
    /**
     * ����������� ������������� ������ �������.
     * @var string
     */
    public $sectionModuleIdIncorrect = 'Incorrect moduleid';
    
    /**
     * ������ �� ������ �� ��������������.
     * @var string
     */
    public $sectionModuleNotFound = 'Module not found (module uid: %1s)';
    
    /**
     * ����������� ��� ������ (�� �������� �������� UfoModule).
     * @var string
     */
    public $sectionModuleIncorrect = 'Module class must extends UfoModule abstract class';
    
    /**
     * ����� ������ ���������� ������� �� ��������� (��������� ������� � ������������� �� ������ ����������� � ������������).
     * @var string
     */
    public $syssectModuleNotDefined = 'Module class not defined';
    
    /**
     * ����������� ��� ������ ���������� ������� (�� �������� �������� UfoSystemModule).
     * @var string
     */
    public $syssectModuleIncorrect = 'Module class must extends UfoSystemModule abstract class';
    
    /**
     * ���������� � URL �������� �� ��������� � ������ (����� UfoModuleParams ��� ��� �������).
     * @var string
     */
    public $moduleParamNotDefined = 'Parameter %1s not identified';
}
