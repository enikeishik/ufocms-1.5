<?php
class UfoDbSettings
{
    protected $host = '';
    protected $user = '';
    protected $password = '';
    protected $name = '';
    protected $charset = '';
    protected $prefix = '';
    
    public function __construct($host, $user, $password, $name, $charset = '', $prefix = '')
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->name = $name;
        $this->charset = $charset;
        $this->prefix = $prefix;
    }
    
    public function getHost() { return $this->host; }
    public function getUser() { return $this->user; }
    public function getPassword() { return $this->password; }
    public function getName() { return $this->name; }
    public function getCharset() { return $this->charset; }
    public function getPrefix() { return $this->prefix; }
}
