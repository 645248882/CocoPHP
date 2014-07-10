<?php

class Com_Cache {

	public static function factory($adapter)
	{
        $class = 'Com_Cache_' . ucfirst($adapter);
        return new $class();
	}
}