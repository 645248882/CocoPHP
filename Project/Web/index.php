<?php
define('APP_PATH', dirname(__DIR__) . '/');

define('SYS_PATH', dirname(APP_PATH) . '/system/');

require SYS_PATH . 'Core/App.php';

Core_App::getInstance()->run();
