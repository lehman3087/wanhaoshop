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
            
                        $activity_type = $_REQUEST['activity_type'];
                        
                        if(($activity_type=='bundlings')||empty($activity_type)){
                          //  $condition, $field = '*', $order = 'bl_id desc', $limit = 0,$page=0
                           $bl=Model('p_bundling')->getBundlingOpenList(array(),'*','bl_id desc',0,$_REQUEST['pageCount']);
                            foreach ($bl as $key => $value) {
                           $goodlist = Model('p_bundling')->getBundlingGoodsList(array('bl_id' =>$value['bl_id'] ));
                           foreach ($goodlist as $key1 => $value1) {
                             $good = Model('goods')->getGoodsInfo(array('goods_id' => $value1['goods_id']), 'goods_id,goods_price,goods_image,goods_name');  
                             $goodlist[$key1]['goods_cost'] =$good['goods_price'];
                             }
                           $bl[$key]['bl_goodlist']=$goodlist;
                        }
                          $activities['bundlings']=$bl; 
                          $count =  Model('p_bundling')->getOpenBundlingCount();
                          
                          
                        }
                        
                        if(($activity_type=='xianshis')||empty($activity_type)){
                            
                           $xs=Model('p_xianshi')->getXianshiList(array(),$_REQUEST['pageCount']);
                            foreach ($xs as $key1 => $value1) {
                           $model_xianshi_goods = Model('p_xianshi_goods');
                            $condition['xianshi_id'] = $value1['xianshi_id'];
                            $xianshi_goods_list = $model_xianshi_goods->getXianshiGoodsExtendList($condition);
                          // $xs_details = Model('p_xianshi_goods')->getXianshiInfoByID($value1['xianshi_id']);
                           $xs[$key1]['xs_goods_list']=$xianshi_goods_list;
                        }
                         $activities['xianshis']=$xs;
                         $count =  Model('p_xianshi')->getXianshiListCount();
                        }
                        
                        if(($activity_type=='groupbuys')||empty($activity_type)){
                            $gb=Model('groupbuy')->getGroupbuyAvailableList(array(),$_REQUEST['pageCount']); 
                            $activities['groupbuys']=$gb;
                            $count =  Model('groupbuy')->getGroupbuyCount();
                        }
                        
                        output_data($activities, mobile_page($count));
                
	}
        
        //平台活动
        public function pfactivityOp($param) {
            //             
             $activity	= Model('activity');
             $act_condition['opening']=true;
             $act_condition['activity_id']=$_REQUEST['activity_id'];
            // $act_condition['join_type']=' left join ';
             
             $page	= new Page();
             $page->setEachNum($_REQUEST['pageCount']);
	     $page->setStyle('admin');
                
             $act_list= $activity->getJoinList($act_condition,$page);
             
             
            // var_dump($page);
             if(!empty($act_list)){
                 output_data($act_list,  mobile_page($page->getTotalPage()));
             }else{
                 output_special_code('10404');
             }
             
             
        }
        
        public function functionName($param) {
            $model_goods = Model('goods');
        $model_search = Model('search');
        
       $post=$this->read_json();  
        $arr=OTA($post);
        $_REQUEST=array_merge($_REQUEST,$arr);
        
        $condition=$this->_dealCondition($_REQUEST['conditions']);

        //查询条件
       // $condition = array();
        if(!empty($_REQUEST['gc_id']) && intval($_REQUEST['gc_id']) > 0) {
            $condition['gc_id'] = $_REQUEST['gc_id'];
        } elseif (!empty($_REQUEST['keyword'])) {
            $condition['goods_name|goods_jingle'] = array('like', '%' . $_REQUEST['keyword'] . '%');
        }
        
        //所需字段
        $fieldstr = "goods_id,goods_commonid,store_id,goods_name,goods_price,goods_marketprice,goods_image,goods_salenum,evaluation_good_star,evaluation_count";

        // 添加3个状态字段
        $fieldstr .= ',is_virtual,is_presell,is_fcode,have_gift';

        //排序方式
        $order = $this->_goods_list_order($_REQUEST['sortType'], $_REQUEST['sortOrder']);
        
        //优先从全文索引库里查找
        list($indexer_ids,$indexer_count) = $model_search->indexerSearch($_REQUEST,$this->page);
        if (is_array($indexer_ids)) {
            //商品主键搜索
            $goods_list = $model_goods->getGoodsOnlineList(array('goods_id'=>array('in',$indexer_ids)), $fieldstr, 0, $order, $this->page, null, false);
            //如果有商品下架等情况，则删除下架商品的搜索索引信息
            if (count($goods_list) != count($indexer_ids)) {
                $model_search->delInvalidGoods($goods_list, $indexer_ids);
            }
            pagecmd('setEachNum',$this->page);
            pagecmd('setTotalNum',$indexer_count);
        } else {
            
            $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, $order, $this->page);
        }
        $page_count = $model_goods->gettotalpage();

        //处理商品列表(抢购、限时折扣、商品图片)
        $goods_list = $this->_goods_list_extend($goods_list);

        output_data(array('goods_list' => $goods_list), mobile_page($page_count));
        }
        
        public function xianshiOp() {

            
        // $model_xianshi = Model('p_xianshi');
        $model_xianshi_goods = Model('p_xianshi_goods');

        $xianshi_id = intval($_REQUEST['xianshi_id']);
//        $xianshi_info = $model_xianshi->getXianshiInfoByID($xianshi_id);
//        
//        if(empty($xianshi_info)) {
//            showDialog(L('param_error'));
//        }
//        Tpl::output('xianshi_info',$xianshi_info);

        //获取限时折扣商品列表
        $condition = array();
        $condition['xianshi_id'] = $xianshi_id;
        $xianshi_goods_list = $model_xianshi_goods->getXianshiGoodsExtendList($condition);
        
        if(!empty($xianshi_goods_list)){
            output_data($xianshi_goods_list);
        }else{
            output_special_code('10404');
        }
        
        
        }
        

}

