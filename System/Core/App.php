<?php
class Core_App {
    protected static $_instance;

    /**
     * 命令模式
     */ 
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
        if (! defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        require SYS_PATH . 'Core' . DS . 'Bootstrap.php';

        // 加载配置助手类
        require SYS_PATH . 'Core' . DS . 'Config.php';

        // 加载核心函数
        require SYS_PATH . 'Core' . DS . 'Function.php';

        // 加载类加载助手类
        require  SYS_PATH . 'Core' . DS . 'Loader.php';

        // 加载全局变量
        Core_Config::load("Global");

        // 框架初始化
        Core_Bootstrap::init();

        // 设置文件检索目录
        set_include_path(
            '.' .
            PATH_SEPARATOR . APP_PATH .
            PATH_SEPARATOR . APP_PATH . DS . 'Library'.
            PATH_SEPARATOR . SYS_PATH
        );

        // 类的自动加载
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

        require $classPath . '.php';

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
                throw new Core_Exception_Fatal('No dispatchInfo found');
            }

            do {
                $this->_dispatched  = true;

                // 执行分发，调用控制器里面的方法，如果这个方法设置了$this->_dispatched为false，将继续分发
                // 这就是forward函数的实现机制
                $this->dispatch();

            } while (! $this->_dispatched);

        } catch (Exception $e) {

            // 命令模式直接退出
            if ($this->_isCli) {
                exit($e);
            }
            // 错误、异常处理控制器
            $dispatchInfo = array(
                'controller' => 'Core_Exception_Error',
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

        // 如果抛出异常，则由异常处理器处理
        if ($controller == 'Core_Exception_Error') {
            $className = $controller;
            $classPath = SYS_PATH . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $controller) . '.php';
        } else {
            $className = "Controller" . "_" . ucfirst($controller);
            $classPath = APP_PATH . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        }

        // 先检测文件是否查找
        if (! file_exists($classPath)) {
            throw new Core_Exception_Fatal('Unable To find file - ' . $classPath);
        }

        // 然后再包含文件
        require $classPath;

        if (! class_exists($className)) {
            throw new Core_Exception_Fatal('Unable to find controller - ' . $classPath);
        }

        $controllerObj = new $className();

        $actionMethod = $action . 'Action';

        // 方法不存在
        if (! method_exists($controllerObj, $actionMethod)) {
            throw new Core_Exception_Fatal('Unable to find action - ' . $className . '::' . $actionMethod, 404);
        }

        $result = call_user_func(array($controllerObj, $actionMethod));

        if (isset($controllerObj->autoRender) && $controllerObj->autoRender) {
            if (null === $result || $result !== false) {
                $tpl = $controllerObj->getTpl() ?: strtolower($controller) . DS . strtolower($action);

                $tplFilePath = template($tpl);

                if (! is_file($tplFilePath)) {
                    throw new Core_Exception_Fatal('Unable to find template - ' . $tplFilePath);
                }

               Core_View::getInstance()->display($tpl);
            }
        }
    }
}