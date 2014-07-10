<?php
abstract class Core_Controller_Abstract
{
    /**
     * 自动加载视图
     *
     * @var bool
     */
    public $autoRender = true;

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

    public function init()
    {

    }

    public function __get($var)
    {
        switch ($var) {
            case '_view':
                return $this->_view = Core_View::getInstance();
                break;
            case '_request':
                return $this->_request = Core_Request::getInstance();
                break;
        }
    }

    public function get($var)
    {
        return $this->_request->get($var);
    }

    public function assign($key, $value = null)
    {
       $this->_view->assign($key, $value);
    }

    public function display($tpl, $data = array(), $return = false)
    {
        $this->_view->display($tpl, $data, $return);
    }

    public function render($tpl, $data  = array())
    {
        $this->_view->display($tpl, $data, true);
    }


    public function getTpl()
    {
        return isset($this->tpl) ? $this->tpl : '';
    }

    public function forward($controller, $action, $param = array())
    {
        $dispatchInfo = array(
            'controller' => $controller,
            'action'     => $action,
            'param'      => $param,
        );

        Core_App::getInstance()->setDispatchInfo($dispatchInfo)->setDispatched(false);
    }
}