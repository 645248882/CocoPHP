<?php

class Core_Router {
    public static function getCliPathInfo()
    {
        return isset($_SERVER['argv'][1]) ? trim($_SERVER['argv'][1], '/') : '';
    }

    public static function getPathInfo()
    {
        $pathInfo = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : '';

        // 当 Nginx 没有配置 PATH_INFO 时的兼容读取（注意修改 rewrite 规则）
        if (! $pathInfo) {
            $pathInfo = isset($_GET['PATH_INFO']) ? $_GET['PATH_INFO'] : '';
        }

        return $pathInfo;
    }

    public static function setPathInfo($pathInfo)
    {
        $_SERVER["PATH_INFO"] = $pathInfo;
    }

    public static function parse($pathInfo)
    {
        $routerConfig = Core_Config::load('Router');

        if (NULL == $pathInfo) {
            $pathInfo = self::getPathInfo();
        }


        $pathInfos = ($pathInfo) ? explode('/', trim($pathInfo, '/')) : array();

        if (! $pathInfos) {
            return $routerConfig["DefaultDispatchInfo"];
        }

        $controller = array_shift($pathInfos);
        $controller = ($controller) ? ucfirst($controller) : $routerConfig["DefaultDispatchInfo"]["controller"];

        $action = array_shift($pathInfos);
        $action = ($action) ? $action : $routerConfig["DefaultDispatchInfo"]["action"];

        // 解析GET参数，例如:
        // _directory/controller/action/k1/v1/k2/v2
        // 从 action 后的 key1 开始为 k/v 组合参数串
        // *因为前面已经两次 array_shift 弹出数组头元素
        // 所以此时的 $pathInfos 数组值已经是： array(k1, v1, k2, v2)，那么再转为关联数组即可 array(k1 => v1, k2 => v2)
        $params = $pathInfos ? Helper_Array::assoc($pathInfos) : array();

        // 解析pathinfo风格URL
        return array(
            'controller' => $controller,
            'action'     => $action,
            'params'     => $params,
        );
    }
}