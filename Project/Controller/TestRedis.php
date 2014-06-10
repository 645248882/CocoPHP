<?php
class Controller_TestRedis extends Core_Controller_Abstract {
    public function indexAction()
    {
        $redis = Com_Cache::factory('redis');
        $redis->set('foo', 'Hello world'); 
        echo $redis->get('foo'); 
    	return false;
    }
}