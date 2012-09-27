<?php
/**
 * Абрстрактный класс CAPTCHA.
 *
 * Этот класс генерирует изображения.
 * Перед показом изображения генерируется случайные данные и 
 * соответствующий им ключ. Ключ и значение заносятся в хранилище. 
 * Изображение генерируется по ключу, выбирая из хранилища данные 
 * и вставляя их в изображение.
 * Следует периодически очищать хранилище от устаревших данных.
 * При проверке принимаются ключ и введенное пользователем значение, 
 * после чего происходит поиск ключа в хранилище и сравниваются 
 * значения в хранилище и пользовательское.
 */
abstract class UfoCaptcha
{
    /**
     * Имя переменной ключа, передаваемой в GET при запросе картинки.
     *
     * @var string
     */
    protected $htmlGetFieldKey = '';
    
    /**
     * Имя поля формы, в котором передается ключ (в POST запросе), 
     * при использовании CAPTCHA.
     *
     * @var string
     */
    protected $htmlPostFieldKey = '';
    
    /**
     * Имя поля формы, в которое посетитель вводит значение CAPTCHA.
     *
     * @var string
     */
    protected $htmlPostFieldValue = '';
    
    /**
     * Время жизни ключей.
     *
     * @var int
     */
    protected $stackLifetime = 600;
    
    
    /**
     * Получение значения CAPTCHA по ключу из хранилища.
     *
     * @param string $key   ключ для выборки по нему данных
     *
     * @return string | false
     */
    abstract protected function fromStack($key);
    
    /**
     * Сохранение ключа и значения в хранилище.
     *
     * @param string $key     ключ
     * @param string $value   значение
     *
     * @return boolean
     */
    abstract protected function intoStack($key, $value);
    
    /**
     * Очистка хранилища от старых данных.
     *
     * @return void
     */
    abstract protected function clearOldStack();
    
    /**
     * Отображение картинки CAPTCHA.
     *
     * @return void
     */
    abstract public function showImage();
    
    /**
     * Возвращает массив данных о CAPTCHA,
     * может использоваться для оформления CAPTCHA в шаблоне.
     *
     * @return array(GetFieldKey, PostFieldKey, PostFieldValue, Ticket)
     */
    abstract public function getCaptcha();
    
    /**
     * Проверяет введенную посетителем CAPTCHA
     *
     * @return boolean
     */
    abstract public function check();
}
