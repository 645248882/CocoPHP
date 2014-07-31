<?php
class Controller_Shop extends Controller_Abstract {
    /**
     * 购买提交
     */
    public function purchaseAction()
    {
        $itemId = $this->getx('item_id');

        if ($itemId < 1) {
        	throws("error item_id");
        }

        $item = Library_Load::loadItem($itemId);

        if (! $item) {
        	throws("error item_id");
        }

        // 检测钻石
        $this->_user->base->checkDiamond($item['price']);

        // 扣除钻石
        $this->_user->base->consumeDiamond($item['price']);
        
        // 增加活力值
		$this->_user->increment('energy', $item['energy']);

		return true;
    }
}