<?php
class Controller_TestRedis extends Core_Controller_Abstract {
    /**
     * 关闭自动渲染视图
     * @var boolean
     */
    public $autoRender = false;

	public function init()
	{
        $this->redis = Com_Cache::factory('redis');
	}

    /**
     * Check the current connection status
     *
     * @return  string STRING: +PONG on success. Throws a RedisException object on connectivity error, as described above.
     */
	public function pingAction()
	{
		pr($this->redis->ping());
		// +PONG
	}

    public function zAddAction()
    {
    	$this->redis->zadd('key', 1, 'v2');
    	$this->redis->zadd('key', 30, 'v5');
    	$this->redis->zadd('key', 5, 'v3');
		$score = $this->redis->zScore('key', 'v3');
    	$size = $this->redis->zsize('key');
    	pr($size); // 3
    }

    public function zCountAction()
    {
    	$this->redis->delete('key');
    	$this->redis->zadd('key', 20, 'v2');
    	$this->redis->zadd('key', 50, 'v5');
    	$this->redis->zadd('key', 30, 'v3');

    	$count = $this->redis->zCount('key', 10, 50);
    	pr($count); // 3 表示有三个元素
    }

    public function zIncrByAction()
    {
    	$this->redis->delete('key');
        $init = $this->redis->zIncrBy('key', 2.5, 'member1'); 
        $res = $this->redis->zIncrBy('key', 1, 'member1'); 
        pr($init);  // 2.5
        pr($res);   // 3.5
    }

    /**
     * 获取两个集合的交集元素
     * @param   string   $keyOutput 交集元素组合成一个新的集合，$ouput为集合的$key
     * @param   array   ZSetKeys  参与运算的两个有序集合
     * @param   array   $Weights  权重，需要分别于之前对应的权重相乘，得到最终的权重
     * @param   string  $aggregateFunction Either "SUM", "MIN", or "MAX":
     * @return [type] [description]
     */
    public function zInterAction()
    {
		$this->redis->delete('k1');
		$this->redis->delete('k2');
		$this->redis->delete('k3');

		$this->redis->delete('ko1');
		$this->redis->delete('ko2');
		$this->redis->delete('ko3');
		$this->redis->delete('ko4');

		$this->redis->zAdd('k1', 0, 'val0');
		$this->redis->zAdd('k1', 1, 'val1');
		$this->redis->zAdd('k1', 3, 'val3');

		$this->redis->zAdd('k2', 2, 'val1');
		$this->redis->zAdd('k2', 3, 'val3');

		$res = $this->redis->zInter('ko1', array('k1', 'k2'));        /* 2, 'ko1' => array('val1', 'val3') */
		$this->redis->zInter('ko2', array('k1', 'k2'), array(1, 1));  /* 2, 'ko2' => array('val1', 'val3') */

		/* Weighted zInter */
		$this->redis->zInter('ko3', array('k1', 'k2'), array(1, 5), 'min'); /* 2, 'ko3' => array('val1', 'val3') */
		$this->redis->zInter('ko4', array('k1', 'k2'), array(1, 5), 'max'); /* 2, 'ko4' => array('val3', 'val1') */
    }

    /**
     * 获取集合的数据 默认根据权重从小到大排列
     * @param   string  $key 集合的key
     * @param   int     $start 0 表示集合的第一个元素
     * @param   int     $end  -1 表示集合的最后一个元素
     * @param   bool    $withscores 
     * @return  array   Array containing the values in specified range.
     */
    public function zRangeAction()
    {
    	$this->redis->delete('key1');
		$this->redis->zAdd('key1', 0, 'val0');
		$this->redis->zAdd('key1', 20, 'val2');
		$this->redis->zAdd('key1', 10, 'val10');
		$res = $this->redis->zRange('key1', 0, -2); /* array('val0', 'val2', 'val10') */

		// with scores 返回的数组的形式为 元素=> 元素的得分
		$res = $this->redis->zRange('key1', 0, -1, true); /* array('val0' => 0, 'val2' => 20, 'val10' => 10) */
   		
   		pr($res);
    }

    /**
     * 获取集合中的元素，元素的权重在指定的范围内，默认的排列顺序为正序
     * @param   string  $key 集合的key
     * @param   int     $start 权重的最小值
     * @param   int     $end  权重的最大值
     * @param   array   $options Two options are available:
     *                      - withscores => TRUE, 获取到的数据形式为元素=>元素的权重
     *                      - and limit => array($offset, $count)
     * @return  array   Array containing the values in specified range.
     */
    public function zRangeByScoreAction()
    {
    	$this->redis->delete('key');
		$this->redis->zAdd('key', 1, 'val0');
		$this->redis->zAdd('key', 6, 'val2');
		$this->redis->zAdd('key', 10, 'val10');
		$res = $this->redis->zRangeByScore('key', 0, 7); /* array('val0', 'val2') */
		
		$res = $this->redis->zRangeByScore('key', 0, 7, array('withscores' => TRUE)); /* array('val0' => 0, 'val2' => 2) */
		
		// 返回的集合倒叙排列，注意，start end也需要倒序
		$res = $this->redis->zRevRangeByScore('key', 7, 0, array('withscores' => TRUE)); /* array('val0' => 0, 'val2' => 2) */
		pr($res);
    }

    /**
     * Returns the rank of a given member in the specified sorted set, starting at 0 for the item
     * with the smallest score. zRevRank starts at 0 for the item with the largest score.
     *
     * @param   string  $key
     * @param   string  $member
     * @return  int     the item's score.
     */
    public function zRankAction()
    {
		$this->redis->delete('key');
		$this->redis->zAdd('key', 3, 'one');
		$this->redis->zAdd('key',5, 'two');
		$this->redis->zAdd('key',6, 'three');
		$rank = $this->redis->zRank('key', 'one'); /* 0 表示排名为第一位*/

		pr($rank);
		$rank = $this->redis->zRank('key', 'two'); /* 1 排名第二位*/
		$rank = $this->redis->zRevRank('key', 'one'); /* 1 */
		$rank = $this->redis->zRevRank('key', 'three'); /* 0 倒序排列，第一位 */
    }

    /**
     * 删除集合中指定的元素
     * 
     * @return int 1：表示成功 0：表示失败
     */
    public function zRemAction()
    {
    	$this->redis->delete("key");
		$this->redis->zAdd('key', 0, 'val0');
		$this->redis->zAdd('key', 2, 'val2');
		$this->redis->zAdd('key', 10, 'val10');
		$this->redis->zDelete('key', 'val2');
		$this->redis->zRem('key', 'val0');
		$res = $this->redis->zRange('key', 0, -1); /* array('val0', 'val10') */

		pr($res);
    }

    /**
     * 根据权重删除集合中的元素
     * @return [type] [description]
     */
    public function zDeleteRangeByRankAction()
    {
    	$this->redis->delete('key');
		$this->redis->zAdd('key', 1, 'one');
		$this->redis->zAdd('key', 2, 'two');
		$this->redis->zAdd('key', 3, 'three');
		$this->redis->zAdd('key', 4, 'four');
		$this->redis->zRemRangeByRank('key', 0, 1); /* 2 */
		$res = $this->redis->zRange('key', 0, -1, TRUE); /* array('three' => 3) */
    	pr($res);
    }

    /**
     * 和zRange不一样的是，返回的元素根据权重倒序排列
     * @return array
     */
    public function zRevRangeAction()
    {
    	$this->redis->delete('key');
		$this->redis->zAdd('key', 0, 'val0');
		$this->redis->zAdd('key', 2, 'val2');
		$this->redis->zAdd('key', 10, 'val10');
		$res = $this->redis->zRevRange('key', 0, -1); /* array('val10', 'val2', 'val0') */

		// with scores
		$this->redis->zRevRange('key', 0, -1, true); /* array('val10' => 10, 'val2' => 2, 'val0' => 0) */
    }

    public function zUnionAction()
    {
    	
    }
}