<?php
/**
 * cms首页
 *
 *
 *
 * by 33hao.com 好商城V3 运营版
 */

//use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class indexControl extends mobileHomeControl{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 首页
     */
	public function indexOp() {
        $model_mb_special = Model('mb_special'); 
        $data = $model_mb_special->getMbSpecialIndex();
        $this->_output_special($data, $_GET['type']);
	}
        
        
        //首页展位列表
        public function adsOp($param) {
            $model = model('rec_position');
            $condition['rec_app'] = 1;
            $condition['rec_state'] = 1;
            $condition['rec_start_time'] =array('lt',time());
            $condition['rec_stop_time'] =array('gt',time());
            
            $rec_list = $model->where($condition)->order('rec_id desc')->select();
            $rec_new_list=array();
            foreach ($rec_list as $key => $value) {
                $rec_list[$key]['content']= unserialize($value['content']);
                //var_dump($rec_list[$key]['content']);
                $rec_new_list[$value['rec_position']][]=$rec_list[$key];
            }
            $rec_new_list2=array();
            foreach ($rec_new_list as $key => $value) {
                $rec_new_list2[$key]['zhanqu_id']=$key;
                $rec_new_list2[$key]['zhanwei_list']=$value;
            }
            $values = array_values($rec_new_list2);
            
            output_data($values);
        }
        
        //新闻列表 平台活动 
        public function smsOp($param) {
            
        }
        
        
        public function getGlobalOp() {
            $model_decoration_style=  Model('decoration');
            $styles=$model_decoration_style->get_style();
            $model_decoration = Model('decoration');
             $category=$model_decoration->get_category();
             $style=$model_decoration->get_style();
             
//             //广告位
//           
//            
//            foreach ($rec_list as $key => $value) {
//                $appplys_model = model('rec_applys');
//                $condition_arr['adp_id'] = $value['rec_id'];
//                $condition_arr['adp_apply_state_in'] = "1";
//                $applys=$appplys_model->getActivitiesJoinList($condition_arr);
//                $rec_list[$key]['adp']=$applys;
//            }
//            
//             //平台活动
//             $activity	= Model('activity');
//             $act_condition['opening']=true;
//             $act_list= $activity->getJoinList($act_condition,$page);
            
             //
            $global=array(
                'snsTracePath'=>'/shop/index.php?act=store_snshome&op=mobileStraceinfo&st_id=',
                'decoration_style'=>$styles,
                'designer_head'=>'',//设计师头像路径
                'category'=>$category,
                'style'=>$style,
                'decoration_work_path'=>'/shop/store/work/',//案例图片地址
                'decoration_request_path'=>'/shop/member/request/',//后跟会员ID
                'rec_list'=>$rec_list,//广告位
                'act_list'=>$act_list,
                'goods_path'=>'/data/upload/shop/store/goods/',//商户商品图像路径
                'store_sns_news_path'=>'/data/upload/shop/store/goods/',//店铺动态图像路径
                'booth_path'=>'/data/upload/shop/rec_position/',//展位基础路径
                'grougbuys_path'=>'/data/upload/shop/groupbuy/'//后跟商户ID 例：/data/upload/shop/groupbuy/1/1_04423393922882448_max.jpg
                
            );
             output_data($global);        
        }

    /**
     * 专题
     */
    public function specialOp() {
        $model_mb_special = Model('mb_special'); 
        $data = $model_mb_special->getMbSpecialItemUsableListByID($_GET['special_id']);
        $this->_output_special($data, $_GET['type'], $_GET['special_id']);
    }

    /**
     * 输出专题
     */
    private function _output_special($data, $type = 'json', $special_id = 0) {
        $model_special = Model('mb_special');
        if($_GET['type'] == 'html') {
            $html_path = $model_special->getMbSpecialHtmlPath($special_id);
            if(!is_file($html_path)) {
                ob_start();
                Tpl::output('list', $data);
                Tpl::showpage('mb_special');
                file_put_contents($html_path, ob_get_clean());
            }
            header('Location: ' . $model_special->getMbSpecialHtmlUrl($special_id));
            die;
        } else {
            output_data($data);
        }
    }

    /**
     * android客户端版本号
     */
    public function apk_versionOp() {
		$version = C('mobile_apk_version');
		$url = C('mobile_apk');
        if(empty($version)) {
           $version = '';
        }
        if(empty($url)) {
            $url = '';
        }

        output_data(array('version' => $version, 'url' => $url));
    }
}
