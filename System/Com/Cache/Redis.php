<?php

class Com_Cache_Redis {
	/**
	 * redis连接实例
	 */
	protected $_redis;

	/**
	 * redis默认配置文件
	 * @var array
	 */
	protected $_config = array(
		        'host'       => '192.168.220.128',
		        'port'       => '6379',
		        'database'   => 0,
		        'timeout'    => 0,
		        'persistent' => true,
		        'options'    => array(),
		    );

	public function __construct()
	{
        $config = Core_Config::load('redis');

        if (!isset($config['default'])) {
            throw new Core_Exception_Fatal('没有找到 ' . $module . ' 模块的 redis 配置信息，请检查 redis.conf.php');
        }

        $this->_config = $config['default'] + $this->_config;
	}

	public function __descruct()
	{
		if ($this->_redis || is_object($this->_redis)) {
			if (method_exists($this->_redis, 'close')) {
				$this->_redis->close();
			} 

			$this->_redis = null;
		}
	}

	protected function _connect()
	{
		if ($this->_redis && is_object($this->_redis)) {
			return $this->_redis;
		}

		try {
			$this->_redis = new redis();
			$func = (isset($this->_config['persistent'])) ? "pconnect" : "connect";
			$this->_redis->$func($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
			
			// 设置附加参数
			if ($this->_config['options']) {
                foreach ($this->_config['options'] as $key => $value) {
                    $this->_redis->setOption($key, $value);
                }
			}
			// 默认连接的数据库
			$this->_redis->select($this->_config['database']);

		} catch (Exception $e) {
			exit("can not connect redis, please check redis.conf.php");
		}
	}

	public function __call($method, $args)
	{
		$this->_connect();

		if (method_exists($this->_redis, $method)) {
			return call_user_func_array(array($this->_redis, $method), $args);
		}
	}
}