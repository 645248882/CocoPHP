<?php
class Controller_User extends Controller_Abstract {
    const 
    	// 回滚一步需要的钻石数量
    	ROLLBACK = 20,
    	// 变换数字需要的钻石数量
    	EXCHANGE = 10;
    public function consumeAction()
    {
    	$type = $this->getx('type');

    	if ($type == 'rollback') {
    		$this->_user->consumeDiamond(self::ROLLBACK);
    	} elseif($type == 'exchange') {
    		$this->_user->consumeDiamond(self::EXCHANGE);
    	}

    	return true;
    }
}