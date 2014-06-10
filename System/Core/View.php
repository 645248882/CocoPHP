<?php
class Core_View {
    private $_storeVars = array();

    private static $_instance;

    private $_tplPath = TPL_PATH;
    private $_tplExt = TPL_EXT;

    public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    public function getStoreVars()
    {
        return $this->_storeVars;
    }

    public function assign($key, $value = null)
    {
        if (! $key) {
           return false;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->assign($k, $v);
            }
        }

        $this->_storeVars[$key] = $value;
    }

    public function display($tpl, $data = array(), $return = false)
    {
        if ($data) {
            $this->_storeVars = array_merge($this->_storeVars, $data);
        }

        if ($this->_storeVars) {
            extract($this->_storeVars);
        }

        // 模板文件完整路径
        $tplFilePath = $this->_tplPath . $tpl . $this->_tplExt;

        if ($return) {
            include $tplFilePath;
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        } else {
            include $tplFilePath;
        }
    }

    public function render($tpl, $data = array())
    {
        return $this->display($tpl, $data, true);
    }

    public function setScriptPath($tplPath)
    {
        $this->_tplPath = $tplPath;
        return $this;
    }

    public function getScriptPath()
    {
        return $this->_tplPath;
    }

}