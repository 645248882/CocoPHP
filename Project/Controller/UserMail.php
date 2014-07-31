<?php
class Controller_UserMail extends Controller_Abstract {

	/**
	 *  获取我的好友邮件
	 */
	public function getMailsAction()
	{
		$list = $this->_user->userMail->getMails();

		foreach ($list as &$value) {
			$value['send_time']  = Helper_Time::getTime(strtotime($value['send_time']));
		}

		pr($list);

		return $list;
	}

	/**
	 * 给好友送能量
	 */ 
	public function sendEnergyAction()
	{
		$toUid = $this->getInt('to_uid');

		if ($toUid < 1) {
			throws("error param");
		}

		if ($this->_uid == $toUid) {
			throws('can not send mail to myself');
		}

		// 检查玩家精力值
		$this->_user->base->checkEnergyEnough();

		// 检查是否是你的好友
		// todo

		if ($this->_user->base->consumeEnergy()) {
			$this->_user->userMail->sendEnergy($toUid);
		}

		return true;
	}

	/**
	 * 接受好友能量
	 */ 
	public function drawEnergyAction()
	{
		$mailId = $this->getInt('mail_id');

		if ($mailId < 1) {
			throws("error param");
		}

		$this->_user->userMail->drawEnergy($mailId);

		return true;
	}
}