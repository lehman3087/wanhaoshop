<?php
/**
 * 推荐展位管理
 **by 好商城V3 www.33hao.com 运营版*/


defined('InShopNC') or exit('Access Invalid!');
class store_promotion_boothControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        //检查是否开启
        if (intval(C('promotion_allow')) !== 1) {
            showMessage(Language::get('promotion_unavailable'), urlShop('seller_center', 'index'),'','error');
        }
    }

    public function indexOp() {
        $this->booth_goods_listOp();
    }

    /**
     * 套餐商品列表
     */
    public function booth_goods_listOp() {
        $model_booth = Model('p_booth');
        // 更新套餐状态
        $where = array();
        $where['store_id'] = $_SESSION['store_id'];
        $where['booth_quota_endtime'] = array('lt', TIMESTAMP);
        $model_booth->editBoothClose($where);

        $hasList = false;
        if (checkPlatformStore()) {
            Tpl::output('isOwnShop', true);
            $hasList = true;
        } else {
            // 检查是否已购买套餐
            $where = array();
            $where['store_id'] = $_SESSION['store_id'];
            $booth_quota = $model_booth->getBoothQuotaInfo($where);
            Tpl::output('booth_quota', $booth_quota);
            if (!empty($booth_quota)) {
                $hasList = true;
            }
        }

        if ($hasList) {
            // 查询已选择商品
            $boothgoods_list = $model_booth->getBoothGoodsList(array('store_id' => $_SESSION['store_id']), 'goods_id');
            if (!empty($boothgoods_list)) {
                $goodsid_array = array();
                foreach ($boothgoods_list as $val) {
                    $goodsid_array[] = $val['goods_id'];
                }
                $goods_list = Model('goods')->getGoodsList(array('goods_id' => array('in', $goodsid_array)), 'goods_id,goods_name,goods_image,goods_price,store_id,gc_id');
                if (!empty($goods_list)) {
                    $gcid_array = array();  // 商品分类id
                    foreach ($goods_list as $key => $val) {
                        $gcid_array[] = $val['gc_id'];
                        $goods_list[$key]['goods_image'] = thumb($val);
                        $goods_list[$key]['url'] = urlShop('goods', 'index', array('goods_id' => $val['goods_id']));
                    }
                    $goodsclass_list = Model('goods_class')->getGoodsClassListByIds($gcid_array);
                    $goodsclass_list = array_under_reset($goodsclass_list, 'gc_id');
                    Tpl::output('goods_list', $goods_list);
                    Tpl::output('goodsclass_list', $goodsclass_list);
                }
            }
        }
        
        $this->profile_menu('booth_goods_list', 'booth_goods_list');
        Tpl::showpage('store_promotion_booth.goods_list');
    }
    
    
    public function booth_pposition_listOp() {
        
        $condition['rec_app']=1;
        $condition['rec_content_type']=3;
        $adp_list = Model('rec_position')->where($condition)->order('rec_id desc')->select();
        
         Tpl::output('ad_positions', $adp_list);
         $this->profile_menu('booth_goods_list', 'booth_pposition_list');
      //  self::profile_menu('booth_renew', 'booth_renew');
        Tpl::showpage('store_promotion_ad_position.list');
    }
    
    
    	/**
	 * 参与活动
	 */
	public function adposition_applyOp(){
                $model_goods	= Model('goods');
		//根据活动编号查询活动信息
		$adp_id = intval($_GET['adp_id']);
		if(adp_id <= 0){
			//showMessage(Language::get('para_error'),'index.php?act=store_promotion_booth&op=store_activity','html','error');
		}
                $condition['rec_id']=$adp_id;
		$adv_info	= Model('rec_position')->where($condition)->find();
		//活动类型必须是商品并且活动没有关闭并且活动进行中
//		if(empty($activity_info) || $activity_info['activity_type'] != '1' || $activity_info['activity_state'] != 1 || $activity_info['activity_start_date']>time() || $activity_info['activity_end_date']<time()){
//			showMessage(Language::get('store_activity_not_exists'),'index.php?act=store_activity&op=store_activity','html','error');
//		}
                
		Tpl::output('adv_info',$adv_info);
                
		$list	= array();//声明存放活动细节的数组
		//查询商品分类列表
		//查询活动细节信息
		$rec_apply_model	= Model('rec_applys');
		$list	= $rec_apply_model->getActivitiesJoinList(array('adp_id'=>"$adp_id",'store_id'=>"{$_SESSION['store_id']}",'group'=>'adp_apply_state asc'));
		//构造通过与审核中商品的编号数组,以便在下方待选列表中,不显示这些内容
                
		$item_ids	= array();
		if(is_array($list) and !empty($list)){
			foreach($list as $k=>$v){
				$item_ids[] = $v['item_id'];
			}
		}
		Tpl::output('list',$list);

		//根据查询条件查询商品列表
		$condition	= array();
		if($_GET['gc_id']!=''){
			$condition['gc_id']	= intval($_GET['gc_id']);
		}
		if($_GET['brand_id']!=''){
			$condition['brand_id']	= intval($_GET['brand_id']);
		}
                
		if(trim($_GET['name'])!=''&&$_REQUEST['rec_content_type']==2){
                    $pos=array();
			//$condition['goods_name'] = array('like' ,'%'.trim($_GET['name']).'%');
                 //       $sql="select * from 33hao_p_xianshi where xianshi_name like %".$_GET['name']."% union all"."select * from 33hao_p_mansong where mansong_name like %".$_GET['name']."% union all"."select * from 33hao_p_bundling where bl_name like %".$_GET['name']."% union all" ;
                        $condition5['bl_name']=array('like' ,'%'.trim($_GET['name']).'%');
                        $condition5['store_id']=$_SESSION['store_id'];
                        $condition5['bl_state']=1;
                        
                        $bl=Model('p_bundling')->getBundlingOpenList($condition5);

                        if(!empty($bl)){
                            foreach ($bl as $key => $value) {
                               $po['type']='p_bundling';
                               $po['id']=$value['bl_id'];
                               $po['name']=$value['bl_name'];
                               $pos[]=$po;
                            }
                         
                        }
//                        $condition2['mansong_name']=array('like' ,'%'.trim($_GET['name']).'%');
//                        $condition2['store_id']=$_SESSION['store_id'];
//                        $condition2['state']=2;
//                        $ms=Model('p_mansong')->getMansongList($condition2);
//                        if(!empty($ms)){
//                            foreach ($ms as $key => $value) {
//                               $po['type']='p_mansong';
//                               $po['id']=$value['mansong_id'];
//                               $po['name']=$value['mansong_name'];
//                                $pos[]=$po;
//                            }
//                          
//                        }
                        $condition3['xianshi_name']=array('like' ,'%'.trim($_GET['name']).'%');
                        $condition3['store_id']=$_SESSION['store_id'];
                        $condition3['start_time']=array('lt',time());
                        $condition3['state']=1;
                        $xs=Model('p_xianshi')->getXianshiList($condition3);
                        if(!empty($xs)){
                            foreach ($xs as $key => $value) {
                                $po['type']='p_xianshi';
                               $po['id']=$value['xianshi_id'];
                               $po['name']=$value['xianshi_name'];
                                $pos[]=$po;
                            }
                        }
                        $condition4['groupbuy_name']=array('like' ,'%'.trim($_GET['name']).'%');
                        $condition4['store_id']=$_SESSION['store_id'];
                        $condition4['state']=20;
                        $gb=Model('groupbuy')->getGroupbuyList($condition4);
                        if(!empty($gb)){
                            foreach ($gb as $key => $value) {
                               $po['type']='groupbuy';
                               $po['id']=$value['groupbuy_id'];
                               $po['name']=$value['groupbuy_name'];
                               $pos[]=$po;
                            }
                           
                        }
                }
                if(trim($_GET['name'])!=''&&$_REQUEST['rec_content_type']==1){
                    
                    if(trim($_GET['name'])!=''){
			$condition['goods_name'] = array('like' ,'%'.trim($_GET['name']).'%');
                    }
                    $condition['store_id']		= $_SESSION['store_id'];
                    
                    if (!empty($item_ids)){
                            $condition['goods_id']	= array('not in', $item_ids);
                    }
		$model_goods	= Model('goods');
		$goods_list	= $model_goods->getGoodsOnlineList($condition,'*', 10);
                
                if(!empty($goods_list)){
                            foreach ($goods_list as $key => $value) {
                               $po['type']='goods';
                               $po['id']=$value['goods_id'];
                               $po['img']=$value['goods_image'];
                               $po['name']=$value['goods_name'];
                               $pos[]=$po;
                            }
                           
                        }
                }
                
                if(trim($_GET['name'])!=''&&$_REQUEST['rec_content_type']==3){
                
                $where['strace_title'] =array('like','%'.$_GET['name'].'%');
                $where['strace_storeid']=$_SESSION['store_id'];
                $where['strace_type'] = 2;    
                $model_stracelog = Model('store_sns_tracelog');
		$strace_array = $model_stracelog->getStoreSnsTracelogList($where, '*', 'strace_id desc', 0, 40);
                
                if(!empty($strace_array)){
                            foreach ($strace_array as $key => $value) {
                               $po['type']='store_sns_tracelog';
                               $po['id']=$value['strace_id'];
                               $po['img']=$value['strace_mb_image'];
                               $po['name']=$value['strace_title'];
                               $pos[]=$po;
                            }
                           
                        }
                }
                
                if(trim($_GET['name'])!=''&&$_REQUEST['rec_content_type']==4){
                    
                    
                 
                 
                 $activity	= Model('activity');
		//条件
		$condition_arr['store_id']		= $_SESSION['store_id'];
		$condition_arr['activity_type'] = '11';//只显示商品活动
		//状态
		//标题
                 $condition_arr['activity_state']=1;
                 
		if (!empty($_GET['name'])){
			$condition_arr['activity_title'] = $_GET['name'];
		}
		$condition_arr['order'] = 'activity_sort asc';
		//活动列表
		$list	= $activity->getList($condition_arr,$page);
                
              //  var_dump($list);
                
		if (!empty($list)){
                    foreach ($list as $key => $value) {
                               $po['type']='activity';
                               $po['id']=$value['activity_id'];
                               $po['img']=$value['activity_banner'];
                               $po['name']=$value['activity_title'];
                               $pos[]=$po;
                            }
			//$condition['goods_id']	= array('not in', $item_ids);
		}
                }
		
               //  $result=@mysql_query($sql);
		Tpl::output('pos',$pos);
		Tpl::output('show_page',$model_goods->showpage());
		Tpl::output('search',$_GET);
		/**
		 * 页面输出
		 */
		$this->profile_menu('booth_goods_list', 'booth_pposition_list');
		Tpl::showpage('store_promotion.apply');
	}
      
        
        
        public function adposition_apply_saveOp(){
		//判断页面参数
            
               
                
		if(empty($_POST['item_id'])){
			showDialog('请勾选内容','index.php?act=store_promotion_booth&op=adposition_apply&adp_id='.$_POST['adp_id'].'&rec_content_type='.$_POST['rec_content_type'].'&name='.$_POST['name']);
		}
                foreach ($_POST['item_id'] as $key=>$item_id){
                    if(empty($_POST['img'][$item_id][name])){
			showDialog('海报不能为空','index.php?act=store_promotion_booth&op=adposition_apply&adp_id='.$_POST['adp_id'].'&rec_content_type='.$_POST['rec_content_type'].'&name='.$_POST['name']);
		}
                }
		$adp_id = intval($_POST['adp_id']);
		if($adp_id <= 0){
			showDialog(Language::get('para_error'),'index.php?act=store_promotion_booth&op=booth_pposition_list');
		}
		//根据页面参数查询活动内容信息，如果不存在则添加，存在则根据状态进行修改
		$rec_model	= Model('rec_position');
                $where['rec_id']=$adp_id;
		$rec	= $rec_model->where($where)->find();
		//活动类型必须是商品并且活动没有关闭并且活动进行中
		if(empty($rec) ||$rec['rec_state'] != '1' || $rec['rec_start_time']>time() || $rec['rec_stop_time']<time()){
			showDialog('广告不存在','index.php?act=store_promotion_booth&op=adposition_apply&adp_id='.$_POST['adp_id']);
		}
		$ra_model	= Model('rec_applys');
		$adps	= $ra_model->getList(array('store_id'=>"{$_SESSION['store_id']}",'adp_id'=>"$adp_id"));
		$ids	= array();//已经存在的活动内容编号
		$ids_state2	= array();//已经存在的被拒绝的活动编号
		if(is_array($list) and !empty($list)){
			foreach ($list as $ad){
				$ids[]	= $ad['item_id'];
				if($ad['adp_apply_state']=='2'){
					$ids_state2[]	= $ad['item_id'];
				}
			}
		}
		//根据查询条件查询商品列表
		foreach ($_POST['item_id'] as $key=>$item_id){
			$item_id = intval($item_id);
			if(!in_array($item_id,$ids)){
				$input	= array();
				$input['adp_id']	= $adp_id;
				$input['item_name']	= $_POST['item_name'][$item_id];
				$input['item_id']	= $item_id;
                                $input['item_cate']	= $_POST['item_cate'][$item_id];
				$input['store_id']	= $_SESSION['store_id'];
				$input['store_name']= $_SESSION['store_name'];
                                $input['rec_img']= $_REQUEST['img'][$item_id]['name'];
//                                var_dump($input);
//                                exit();
				$ra_model->add($input);
			}elseif(in_array($item_id,$ids_state2)){
				$input	= array();
				$input['adp_apply_state']= '0';//将重新审核状态去除
				$ra_model->updateList($input,array('item_id'=>$item_id));
			}
		}
		showDialog('已提交','reload','succ');
	}
        

    /**
     * 选择商品
     */
    public function booth_select_goodsOp() {
        $model_goods = Model('goods');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        if ($_POST['goods_name'] != '') {
            $condition['goods_name'] = array('like', '%'.$_POST['goods_name'].'%');
        }
        $goods_list = $model_goods->getGoodsOnlineList($condition, '*', 10);

        Tpl::output('goods_list', $goods_list);
        Tpl::output('show_page', $model_goods->showpage());
        Tpl::showpage('store_promotion_booth.select_goods', 'null_layout');
    }

    /**
     * 购买套餐
     */
    public function booth_quota_addOp() {
        if (chksubmit()) {
            $quantity = intval($_POST['booth_quota_quantity']); // 购买数量（月）
            $price_quantity = $quantity * intval(C('promotion_booth_price')); // 扣款数
            if ($quantity <= 0 || $quantity > 12) {
                showDialog('参数错误，购买失败。', urlShop('store_promotion_booth', 'booth_quota_add'), '', 'error' );
            }
            // 实例化模型
            $model_booth = Model('p_booth');

            $data = array();
            $data['store_id']               = $_SESSION['store_id'];
            $data['store_name']             = $_SESSION['store_name'];
            $data['booth_quota_starttime']  = TIMESTAMP;
            $data['booth_quota_endtime']    = TIMESTAMP + 60 * 60 * 24 * 30 * $quantity;
            $data['booth_state']            = 1;

            $return = $model_booth->addBoothQuota($data);
            if ($return) {
                // 添加店铺费用记录
                $this->recordStoreCost($price_quantity, '购买推荐展位');

                // 添加任务队列
                $end_time = TIMESTAMP + 60 * 60 * 24 * 30 * $quantity;
                $this->addcron(array('exetime' => $end_time, 'exeid' => $_SESSION['store_id'], 'type' => 4), true);
                $this->recordSellerLog('购买'.$quantity.'套推荐展位，单位元');
                showDialog('购买成功', urlShop('store_promotion_booth', 'booth_goods_list'), 'succ');
            } else {
                showDialog('购买失败', urlShop('store_promotion_booth', 'booth_quota_add'));
            }
        }
        // 输出导航
        self::profile_menu('booth_quota_add', 'booth_quota_add');
        Tpl::showpage('store_promotion_booth.quota_add');
    }

    /**
     * 套餐续费
     */
    public function booth_renewOp() {
        if (chksubmit()) {
            $model_booth = Model('p_booth');
            $quantity = intval($_POST['booth_quota_quantity']); // 购买数量（月）
            $price_quantity = $quantity * intval(C('promotion_booth_price')); // 扣款数
            if ($quantity <= 0 || $quantity > 12) {
                showDialog('参数错误，购买失败。', urlShop('store_promotion_booth', 'booth_quota_add'), '', 'error' );
            }
            $where = array();
            $where['store_id'] = $_SESSION ['store_id'];
            $booth_quota = $model_booth->getBoothQuotaInfo($where);
            if ($booth_quota['booth_quota_endtime'] > TIMESTAMP) {
                // 套餐未超时(结束时间+购买时间)
                $update['booth_quota_endtime']   = intval($booth_quota['booth_quota_endtime']) + 60 * 60 * 24 * 30 * $quantity;
            } else {
                // 套餐已超时(当前时间+购买时间)
                $update['booth_quota_endtime']   = TIMESTAMP + 60 * 60 * 24 * 30 * $quantity;
            }
            $return = $model_booth->editBoothQuotaOpen($update, $where);

            if ($return) {
                // 添加店铺费用记录
                $this->recordStoreCost($price_quantity, '购买推荐展位');

                // 添加任务队列
                $end_time = TIMESTAMP + 60 * 60 * 24 * 30 * $quantity;
                $this->addcron(array('exetime' => $end_time, 'exeid' => $_SESSION['store_id'], 'type' => 4), true);
                $this->recordSellerLog('续费'.$quantity.'套推荐展位，单位元');
                showDialog('购买成功', urlShop('store_promotion_booth', 'booth_list'), 'succ');
            } else {
                showDialog('购买失败', urlShop('store_promotion_booth', 'booth_quota_add'));
            }
        }

        self::profile_menu('booth_renew', 'booth_renew');
        Tpl::showpage('store_promotion_booth.quota_add');
    }

    /**
     * 选择商品
     */
    public function choosed_goodsOp() {
        $gid = $_GET['gid'];
        if ($gid <= 0) {
            $data = array('result' => 'false', 'msg' => '参数错误');
            $this->_echoJson($data);
        }

        // 验证商品是否存在
        $goods_info = Model('goods')->getGoodsInfoByID($gid, 'goods_id,goods_name,goods_image,goods_price,store_id,gc_id');
        if (empty($goods_info) || $goods_info['store_id'] != $_SESSION['store_id']) {
            $data = array('result' => 'false', 'msg' => '参数错误');
            $this->_echoJson($data);
        }

        $model_booth = Model('p_booth');

        if (!checkPlatformStore()) {
            // 验证套餐时候过期
            $booth_info = $model_booth->getBoothQuotaInfo(array('store_id' => $_SESSION['store_id'], 'booth_quota_endtime' => array('gt', TIMESTAMP)), 'booth_quota_id');
            if (empty($booth_info)) {
                $data = array('result' => 'false', 'msg' => '套餐过期请重新购买套餐');
                $this->_echoJson($data);
            }
        }

        // 验证已添加商品数量，及选择商品是否已经被添加过
        $bootgoods_info = $model_booth->getBoothGoodsList(array('store_id' => $_SESSION['store_id']), 'goods_id');
        // 已添加商品总数
        if (count($bootgoods_info) >= C('promotion_booth_goods_sum')) {
            $data = array('result' => 'false', 'msg' => '只能添加'.C('promotion_booth_goods_sum').'个商品');
            $this->_echoJson($data);
        }
        // 商品是否已经被添加
        $bootgoods_info = array_under_reset($bootgoods_info, 'goods_id');
        if (isset($bootgoods_info[$gid])) {
            $data = array('result' => 'false', 'msg' => '商品已经添加，请选择其他商品');
            $this->_echoJson($data);
        }

        // 保存到推荐展位商品表
        $insert = array();
        $insert['store_id'] = $_SESSION['store_id'];
        $insert['goods_id'] = $goods_info['goods_id'];
        $insert['gc_id'] = $goods_info['gc_id'];
        $model_booth->addBoothGoods($insert);

        $this->recordSellerLog('添加推荐展位商品，商品id：'.$goods_info['goods_id']);

        // 输出商品信息
        $goods_info['goods_image'] = thumb($goods_info);
        $goods_info['url'] = urlShop('goods', 'index', array('goods_id' => $goods_info['goods_id']));
        $goods_class = Model('goods_class')->getGoodsClassInfoById($goods_info['gc_id']);
        $goods_info['gc_name'] = $goods_class['gc_name'];
        $goods_info['result'] = 'true';
        $this->_echoJson($goods_info);
    }
    

    /**
     * 删除选择商品
     */
    public function del_choosed_goodsOp() {
        $gid = $_GET['gid'];
        if ($gid <= 0) {
            $data = array('result' => 'false', 'msg' => '参数错误');
            $this->_echoJson($data);
        }

        $result = Model('p_booth')->delBoothGoods(array('goods_id' => $gid, 'store_id' => $_SESSION['store_id']));
        if ($result) {
            $this->recordSellerLog('删除推荐展位商品，商品id：'.$gid);
            $data = array('result' => 'true');
        } else {
            $data = array('result' => 'false', 'msg' => '删除失败');
        }
        $this->_echoJson($data);
    }

    /**
     * 输出JSON
     * @param array $data
     */
    private function _echoJson($data) {
        if (strtoupper(CHARSET) == 'GBK'){
            $data = Language::getUTF8($data);//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
        }
        echo json_encode($data);exit();
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string	$menu_type	导航类型
     * @param string 	$menu_key	当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key='') {
        $menu_array	= array();
        switch ($menu_type) {
            case 'booth_goods_list':
                $menu_array	= array(
                    1=>array('menu_key'=>'booth_goods_list', 'menu_name'=>'商品列表', 'menu_url'=>urlShop('store_promotion_booth', 'booth_goods_list')),
                    2=>array('menu_key'=>'booth_pposition_list', 'menu_name'=>'广告位申请', 'menu_url'=>urlShop('store_promotion_booth', 'booth_pposition_list'))
                    );
                break;
            case 'booth_quota_add':
                $menu_array = array(
                    1=>array('menu_key'=>'booth_goods_list', 'menu_name'=>'商品列表', 'menu_url'=>urlShop('store_promotion_booth', 'booth_goods_list')),
                    2=>array('menu_key'=>'booth_quota_add', 'menu_name'=>'购买套餐', 'menu_url'=>urlShop('store_promotion_booth', 'booth_quota_add'))
                );
                break;
            case 'booth_renew':
                $menu_array = array(
                    1=>array('menu_key'=>'booth_goods_list', 'menu_name'=>'商品列表', 'menu_url'=>urlShop('store_promotion_booth', 'booth_goods_list')),
                    2=>array('menu_key'=>'booth_renew', 'menu_name'=>'套餐续费', 'menu_url'=>urlShop('store_promotion_booth', 'booth_renew'))
                );
                break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
