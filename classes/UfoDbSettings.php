<?php
class UfoDbSettings
{
    private $host = '';
    private $user = '';
    private $password = '';
    private $name = '';
    private $prefix = '';
    
    public function __construct($host, $user, $password, $name, $prefix = '')
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->name = $name;
        $this->prefix = $prefix;
    }
    
    public function getHost() { return $this->host; }
    public function getUser() { return $this->user; }
    public function getPassword() { return $this->password; }
    public function getName() { return $this->name; }
    public function getPrefix() { return $this->prefix; }
}
