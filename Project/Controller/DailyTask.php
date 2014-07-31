<?php

/**
 * 每日任务 控制器
 */

class Controller_Dailytask extends Controller_Abstract
{
	/**
	 * 领取任务完成奖励
	 */ 
	public function drawAwardAction()
	{
        $taskId = $this->getInt('task_id');

        if ($taskId < 1) {
            throws('Invalid DailyTaskId');
        }

        $result = $this->_user->dailyTask->drawAward($taskId);
	}

	/**
	 * 领取大奖
	 */ 
	public function drawBigAwardAction()
	{
		$this->_user->dailyTask->drawBigAward();
	}

	/**
	 * 每日任务完成
	 */ 
	public function finishAction()
	{
        $taskId = $this->getInt('task_id');

        if ($taskId < 1) {
            throws('Invalid DailyTaskId');
        }

        $this->_user->dailyTask->finish($taskId);
	}
}