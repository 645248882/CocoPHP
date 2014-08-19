<?php

define('APP_PATH', dirname(__DIR__) . '/');

define('SYS_PATH', dirname(APP_PATH) . '/System/');

require SYS_PATH . 'Core/App.php';

Core_App::getInstance()->run();


