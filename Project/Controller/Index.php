<?php
class Controller_Index extends Core_Controller_Abstract {
    public function indexAction()
    {
        $redis = Com_Cache::factory('redis');
        $redis->set('foo', 'Hello world'); 
        echo $redis->get('foo'); 
    	return false;
    }

    public function testAction()
    {
    	echo "this is test";
    }
}