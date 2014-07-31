<?php
abstract class Core_Controller_Web
{
    /**
     * 自动加载视图
     *
     * @var bool
     */
    public $autoRender = false;

    /**
     * 视图模板名称
     *
     * @var string
     */
    public $tpl = '';

    private $_tplPath = "";

    final public function __construct()
    {
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    public function __get($var)
    {
        switch ($var) {
            case '_request':
                return $this->_request = Core_Request::getInstance();
                break;
        }
    }

    public function get($var)
    {
        return $this->_request->get($var);
    }

    public function getx($key)
    {
        $value = $this->_request->get($key);
        return Helper_String::deepFilterDatasInput($value);
    }

    public function getInt($key)
    {
        return intval($this->_request->get($key));
    }

    public function getInts($key)
    {
        $value = $this->get($key);
        return is_array($value) ? array_filter(array_map('intval', $value)) : intval($value);
    }

    public function getPost($key = null, $default = null)
    {
        return $this->_request->getPost($key, $default);
    }

    public function getParam($key = null, $default = null)
    {
        return $this->_request->getParam($key, $default);
    }

    public function getParams()
    {
        return $this->_request->getParams();
    }

    public function getQuery($key = null, $default = null)
    {
        return $this->_request->getQuery($key, $default);
    }

    public function getQueryx($key = null, $default = null)
    {
        return Helper_String::deepFilterDatas($this->getQuery($key, $default), array('strip_tags', 'trim'));
    }

    public function json($output)
    {
        header('Content-type: text/json');
        header('Content-type: application/json; charset=UTF-8');
        exit(json_encode($output));
    }

}