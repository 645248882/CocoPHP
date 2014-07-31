<?php
class Dao_UserDevice extends Dao_Abstract {

	protected $_tableName = 'user_device';
	public function getUidByDevice($token)
	{
		return $this->where(array('token' => $token))->fetchOne();
	}
}