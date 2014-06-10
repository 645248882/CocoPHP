<?php
class Core_App {
    protected static $_instance;

    protected $_isCli = false;

    /**
     * 本请求是否已完成所有分发
     *
     * @var bool
     */
    private $_dispatched = true;

    protected $_dispatchInfo = array();

    protected function __construct()
    {
        // Framework init
        require SYS_PATH . 'Core/Bootstrap.php';
        Core_Bootstrap::init();

        // include 检索目录设置
        set_include_path(
            '.' .
            PATH_SEPARATOR . APP_PATH .
            PATH_SEPARATOR . SYS_PATH
        );

        // Autoload class
        spl_autoload_register(array($this, 'autoload'));
    }

    public static function getInstance()
    {
        if (! self::$_instance && ! is_object(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function autoload($className)
    {
        $classPath = $className;
        if (strpos($className, '_') !== false) {
            $classPath = str_replace('_', DIRECTORY_SEPARATOR, $className);
        }

        require_once $classPath . '.php';

        // 包含文件之后，在检测类是否存在
        if (! class_exists($className, false) && ! interface_exists($className, false)) {
            throw new Core_Exception_Fatal('Unable to load class: ' . $className);
        }
    }

    public function run()
    {
        try {
            // 路由解析
            $this->setDispatchInfo();

            if (! $this->_dispatchInfo) {
                throw new Exception('No dispatchInfo found');
            }

            do {
                $this->_dispatched  = true;
                // 执行分发
                $this->dispatch();

            } while (! $this->_dispatched);

        } catch (Exception $e) {

            // 命令模式直接退出
            if ($this->_isCli) {
                exit($e);
            }
            // 错误、异常处理控制器
            $dispatchInfo = array(
                'controller' => 'error',
                'action'     => 'error',

                // 把异常传递到异常控制器中
                'params'     => array(
                    'exception' => $e,
                ),
            );

            $this->setDispatchInfo($dispatchInfo);

            $this->dispatch();
        }
    }

    public function setDispatched($dispatched = true)
    {
        $this->_dispatched = $dispatched;

        return $this;
    }

    public function setDispatchInfo($dispatchInfo = null)
    {
        if (null == $dispatchInfo) {
            $pathInfo = $this->_isCli ? Core_Router::getCliPathInfo() : Core_Router::getPathInfo();
            $dispatchInfo = Core_Router::parse($pathInfo);
        }

        $this->_dispatchInfo = $dispatchInfo;

        return $this;
    }

    public function dispatch()
    {
        $controller = $this->_dispatchInfo['controller'];
        $action     = $this->_dispatchInfo['action'];
        $params     = $this->_dispatchInfo['params'];

        // 储存URL参数
        if ($params) {
            Core_Request::getInstance()->setParams($params);
        }

        $className = "Controller" . "_" . $controller;

        $classPath = APP_PATH . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (! file_exists($classPath)) {
            throw new Exception('Unable to find controller - ' . $classPath);
        }

        require $classPath;

        if (! class_exists($className)) {
            throw new Exception('Unable to find controller - ' . $classPath);
        }

        $controllerObj = new $className();

        $actionMethod = $action . 'Action';

        // 方法不存在
        if (! method_exists($controllerObj, $actionMethod)) {
            throw new Exception('Unable to find action - ' . $className . '::' . $actionMethod, 404);
        }

        $result = call_user_func(array($controllerObj, $actionMethod));

        // 视图自动渲染
        if (isset($controllerObj->autoRender) && $controllerObj->autoRender) {
            // 如果没有返回值，或者返回值为true 则自动渲染视图
            // 如果返回为false，则不渲染视图
            if (null === $result || $result !== false) {
                // 获取模板文件名,默认文件目录为controller,默认文件为action
                $tpl = $controllerObj->getTpl() ?: strtolower($controller) . DS . strtolower($action);

                // 获取模板文件名路径
                $tplFilePath = template($tpl);

                // 检测模板文件是否存在
                if (! is_file($tplFilePath)) {
                    throw new Exception('Unable to find template - ' . $tplFilePath);
                }

                // 自动渲染
                Core_View::getInstance()->display($tpl);
            }
        }
    }
}