<?php

class Core_Request {
    private static $_instance  = '';
    private $_params = array();

    public function __construct()
    {
        // 保证GPC都是没有经过 addslashes
        if (get_magic_quotes_gpc()) {
            $_GET     = $this->stripslashes($_GET);
            $_POST    = $this->stripslashes($_POST);
            $_COOKIE  = $this->stripslashes($_COOKIE);
            $_REQUEST = $this->stripslashes($_REQUEST);
        }
    }

    public function __get($key)
    {
        switch (true) {
            case isset($this->_params[$key]):
                return $this->_params[$key];
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            case isset($_SERVER[$key]):
                return $_SERVER[$key];
            case isset($_ENV[$key]):
                return $_ENV[$key];
            default:
                return null;
        }
    }

    public function get($key)
    {
        return $this->__get($key);
    }

    public function stripslashes($data)
    {
        return is_array($data) ? array_map(array($this, 'stripslashes'), $data) : stripslashes($data);
    }

    public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getParams()
    {
        $return = $this->_params;

        if (isset($_GET) && is_array($_GET)) {
            $return += $_GET;
        }
        if (isset($_POST) && is_array($_POST)) {
            $return += $_POST;
        }

        return $return;
    }

    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }

        return $this;
    }

    public function setParam($key, $value)
    {
        $key = (string) $key;

        if ((null === $value) && isset($this->_params[$key])) {
            unset($this->_params[$key]);
        } elseif (null !== $value) {
            $this->_params[$key] = $value;
        }

        return $this;
    }

    public function getQuery($key = null, $default = null)
    {
        if (null === $key) {
            return $_GET;
        }

        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }

    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }

        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }

    public function getCookie($key = null, $default = null)
    {
        if (null === $key) {
            return $_COOKIE;
        }

        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }

    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    public function getEnv($key = null, $default = null)
    {
        if (null === $key) {
            return $_ENV;
        }

        return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
    }
}