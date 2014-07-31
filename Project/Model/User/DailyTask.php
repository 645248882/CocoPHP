<?php

/**
 * 用户每日任务
 */

class Model_User_DailyTask extends Model_User_Trait {

	public static $taskIds = array(2001, 2002, 2003);

    public static $awards = array(
        '2001' => array('energy' => 1),
        '2002' => array('energy' => 1),
        '2003' => array('energy' => 1),
        '0000' => array('diamond' => 20),
        );

	public function drawAward($taskId)
	{
        if ($taskId < 1 || ! in_array($taskId, self::$taskIds)) {
            throws('无效 DailyTaskId');
        }

        $where = array('today' => TODAY, 'uid' => $this->_uid);
        $userTask = Dao('UserDailyTask')->where($where)->fetchRow();

        if (! $userTask || ! $userTask['task_ids']) {
            throws("任务未完成, 不能领奖");
        }

        $taskIds = explode(',', $userTask['task_ids']);

        if (! in_array($taskId, $taskIds)) {
            throws('任务未完成, 不能领奖');
        }

        $drawAwardIds = array();
        if ($userTask['draw_task_ids']) {
            $drawTaskIds = explode(',', $userTask['draw_task_ids']);
            if (in_array($taskId, $drawTaskIds)) {
                throws("已经领奖不能重复领取");
            }
        }

        // 领取奖励
        $award = self::$awards[$taskId];

        $setArr = array();
        foreach ($award as $field => $num) {
            $setArr[$field] = array('+', $num);
        }

        $this->_user->update($setArr);

        // 设置奖励为已经领取
        $drawTaskIds[] = $taskId;
        $drawTaskIds = implode(',', $drawTaskIds);
        Dao('UserDailyTask')->where($where)->update(array('draw_task_ids' => $drawTaskIds));

        return array(
            'energy' => $this->_user['energy'],
            'diamond' => $this->_user['diamond'],
        );
	}

	public function finish($taskId)
	{
        if ($taskId < 1) {
            throws('Invalid DailyTaskId');
        }

        $where = array('today' => TODAY, 'uid' => $this->_uid);
        $taskIds = Dao('UserDailyTask')->where($where)->fetchRow();

        if (! in_array($taskId, self::$taskIds)) {
            throws('Invalid DailyTaskId');
        } 

        if ($taskIds) {
        	$taskIds = explode(',', trim($taskIds['task_ids'], ','));

	        if (in_array($taskId, $taskIds)) {
	        	throws('Hava finish this DailyTask');
	        }

	        $taskIds[] = $taskId;

        	$taskIds = implode(',', $taskIds);

        	Dao('UserDailyTask')->where($where)->update(array('task_ids' => $taskIds));
        } else {
			$setArr = array(
				'uid'      => $this->_uid,
				'today'    => TODAY,
				'task_ids' => $taskId
			);

        	Dao('UserDailyTask')->insert($setArr);
        }

        return true;
	}

	public function drawBigAward()
	{
        $where = array('today' => TODAY, 'uid' => $this->_uid);
        $taskIds = Dao('UserDailyTask')->where($where)->fetchRow();
        $drawAwardIds = array();
        if ($userTask['task_ids']) {
            $finishTaskIds = explode(',', trim($userTask['task_ids'], ','));

            $res = array_diff($finishTaskIds, self::$taskIds);

            if ($res) {
                throws("还有任务没有完成");
            }

            $award = self::$awards['0000'];

            $setArr = array();
            foreach ($award as $field => $num) {
                $setArr[$field] = array('+', $num);
            }

            $this->_user->update($setArr);

            return array(
                'energy' => $this->_user['energy'],
                'diamond' => $this->_user['diamond'],
            );
        }
	}
}