<?php

/**
 * 商店购买配置文件
 */ 
class Library_Load {
	public static $item = array(
		'1001' => array(
			'energy' => 10,
			'price'  => 35,
		),

		'1002' => array(
			'energy' => 20,
			'price'  => 60,
		),

		'1003' => array(
			'energy' => 40,
			'price'  => 110,
		),		

		'1004' => array(
			'energy' => 100,
			'price'  => 265,
		)
	);

	public static function loadItem($itemId)
	{
		if (isset(self::$item[$itemId])) {
			return self::$item[$itemId];
		}

		return false;
	}
}