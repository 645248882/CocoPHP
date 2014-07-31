<?php

/**
 * 好友送星
 */

class Model_User_UserMail extends Model_User_Trait {

	public function sendEnergy($toUid) 
	{
		$toUser = new Model_User($toUid);

		$setArr = array(
			'from_uid'  => $this->_uid,
			'to_uid'    => $toUser['uid'],
			'content'   => $toUser['user_name'] . "赠送了你一点活力值",
			'award'     => json_encode(array('energy' => 1)),
			'send_time' => $GLOBALS['_DATE'],
		);

		return Dao('UserMail')->insert($setArr);
	}

	public function drawEnergy($mailId)
	{
		if ($mailId < 1) {
			throws("error mail_id");
		}

		// 读取邮件
		$mail = Dao('UserMail')->get($mailId);

		if (! $mail || $mail['to_uid'] != $this->_uid) {
			throws("is not your mail");
		}

		if ($mail['is_draw'] == 1) {
			throws("you have drawn this mail");
		}

		// 设置邮件为已读
		Dao('UserMail')->updateByPk(array('is_draw' => 1), $mailId);

		return true;
	}

	public function getMails()
	{
		return Dao('UserMail')->where(array('is_draw' => 0, 'to_uid' => $this->_uid))->order('send_time DESC')->fetchAll();
	}
}