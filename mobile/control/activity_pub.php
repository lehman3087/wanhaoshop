<?php
/**
 * 购买
 *
 *
 *
 *
 * by 33hao.com 好商城V3 运营版
 */

//use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class activity_pubControl extends mobileHomeControl {

	public function __construct() {
		parent::__construct();
	}
        /**
	 * 所有优惠
	 */
        public function youhuisOp(){
                        $bl=Model('p_bundling')->getBundlingOpenList($condition5);
                        foreach ($bl as $key => $value) {
                           $goodlist = Model('p_bundling')->getBundlingGoodsList(array('bl_id' =>$value['bl_id'] ));
                           foreach ($goodlist as $key1 => $value1) {
                             $good = Model('goods')->getGoodsInfo(array('goods_id' => $value1['goods_id']), 'goods_id,goods_price,goods_image,goods_name');  
                             $goodlist[$key1]['goods_cost'] =$good['goods_price'];
                             }
                           $bl[$key]['bl_goodlist']=$goodlist;
                        }
                        $activities['bundlings']=$bl;
                        $xs=Model('p_xianshi')->getXianshiList();
                        foreach ($xs as $key1 => $value1) {
                           $model_xianshi_goods = Model('p_xianshi_goods');
                            $condition['xianshi_id'] = $value1['xianshi_id'];
                            $xianshi_goods_list = $model_xianshi_goods->getXianshiGoodsExtendList($condition);
                          // $xs_details = Model('p_xianshi_goods')->getXianshiInfoByID($value1['xianshi_id']);
                           $xs[$key1]['xs_goods_list']=$xianshi_goods_list;
                        }
                         $activities['xianshis']=$xs;
                        $gb=Model('groupbuy')->getGroupbuyAvailableList(); 
                        $activities['groupbuys']=$gb;
                        output_data($activities);
                
	}
        

}

