<?php
/**
 * ����������� �����-���������, ������������ ��� �������� ������.
 */
abstract class UfoStruct
{
    /**
     * ����������� ������-��������� ��������� ��������� ���� �������, 
     * ������������� ����������� �������������� �������, 
     * � ������� ����� ������������� ������ ����� ������.
     * ��� ������������ ����� �������� �������������� ���������� 
     * � ���� ����, ������� ������������ ��������� ���� ��-���������.
     *
     * @param array $vars = null    ������������� ������ � �������
     */
    public function __construct(array $vars = null)
    {
        if (is_null($vars)) {
            return;
        }
        foreach ($vars as $key => $val) {
            if (isset($this->$key)) {
                if (is_int($this->$key)) {
                	$this->$key = (int) $val;
                } else if (is_string($this->$key)) {
                	$this->$key = (string) $val;
                } else if (is_bool($this->$key)) {
                	$this->$key = (bool) $val;
                } else if (is_float($this->$key)) {
                	$this->$key = (float) $val;
                } else {
            		$this->$key = $val;
                }
            }
        }
    }
}
