<?php
/**
 * 推荐位
 *
 *
 *
 **by 好商城V3 www.33hao.com 运营版*/

defined('InShopNC') or exit('Access Invalid!');
class rec_positionControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('rec_position');
	}

        
        	/**
	 * 活动列表/删除活动
	 */
	public function activityOp(){
		$activity	= Model('activity');
		//条件
		$condition_arr = array();
		$condition_arr['activity_type'] = '1';//只显示商品活动
		//状态
		if (!empty($_GET['searchstate'])){
			$state = intval($_GET['searchstate'])-1;
			$condition_arr['activity_state'] = "$state";
		}
		//标题
		if (!empty($_GET['searchtitle'])){
			$condition_arr['activity_title'] = $_GET['searchtitle'];
		}
		//有效期范围
		if (!empty($_GET['searchstartdate']) && !empty($_GET['searchenddate'])){
			$condition_arr['activity_daterange']['startdate'] = strtotime($_GET['searchstartdate']);
			$condition_arr['activity_daterange']['enddate'] = strtotime($_GET['searchenddate']);
            if($condition_arr['activity_daterange']['enddate'] > 0) {
                $condition_arr['activity_daterange']['enddate'] += 86400;
            }
		}
		$condition_arr['order'] = 'activity_sort asc';
		//活动列表
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$list	= $activity->getList($condition_arr,$page);
		//输出
		Tpl::output('show_page',$page->show());
		Tpl::output('list',$list);
		Tpl::showpage('activity.index');
	}
        
           /**
	 * 申请细节列表
	 */
	public function detailOp(){
		$rec_id = intval($_GET['rec_id']);
		if($rec_id <= 0){
			showMessage(Language::get('miss_argument'));
		}
		//条件
		$condition_arr = array();
		$condition_arr['adp_id'] = $rec_id;
		//审核状态
		if (!empty($_GET['searchstate'])){
			$state = intval($_GET['searchstate'])-1;
			$condition_arr['adp_apply_state'] = "$state";
		}
		//店铺名称
		if (!empty($_GET['searchstore'])){
			$condition_arr['store_name'] = $_GET['searchstore'];
		}
                		//店铺名称
		if (!empty($_GET['searchapplys'])){
			$condition_arr['item_name'] = $_GET['searchapplys'];
		}
		$condition_arr['order'] = 'rec_applys.adp_apply_state asc,rec_applys.adp_apply_sort asc';

		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$rec_model	= Model('rec_applys');
		$list	= $rec_model->getList($condition_arr,$page);
		//输出到模板
		Tpl::output('show_page',$page->show());
		Tpl::output('list',$list);
		Tpl::showpage('rec_detail.index');
	}

        /**
	 * 活动内容处理
	 */
	public function dealOp(){
		if(empty($_REQUEST['adp_apply_id'])){
			showMessage(Language::get('activity_detail_del_choose_detail'));
		}
		//获取id
		$id	= '';
		if(is_array($_POST['adp_apply_id'])){
			$id	= "'".implode("','",$_POST['adp_apply_id'])."'";
		}else{
			$id	= intval($_GET['adp_apply_id']);
		}
		//创建活动内容对象
		$rec_applys	= Model('rec_applys');
		if($rec_applys->update(array('adp_apply_state'=>intval($_GET['state'])),$id)){
			$this->log(L('nc_edit,rec_position_index').'[ID:'.$id.']',null);
			showMessage(Language::get('nc_common_op_succ'));
		}else{
			showMessage(Language::get('nc_common_op_fail'));
		}
	}

	/**
	 * 删除活动内容
	 */
	public function del_detailOp(){
		if(empty($_REQUEST['adp_apply_id'])){
			showMessage(Language::get('activity_detail_del_choose_detail'));
		}
		$id	= '';
		if(is_array($_POST['adp_apply_id'])){
			$id	= "'".implode("','",$_POST['adp_apply_id'])."'";
		}else{
			$id	= "'".intval($_GET['adp_apply_id'])."'";
		}
		$rec_applys_detail	= Model('rec_applys');
		//条件
		$condition_arr = array();
		$condition_arr['adp_apply_id_in'] = $id;
		$condition_arr['adp_apply_state_in'] = "'0','2'";//未审核和已拒绝
		if($rec_applys_detail->delList($condition_arr)){
			$this->log(L('nc_del,activity_index_content').'[ID:'.$id.']',null);
			showMessage(Language::get('nc_common_del_succ'));
		}else{
			showMessage(Language::get('nc_common_del_fail'));
		}
	}
        
        
	/**
	 * 推荐位列表
	 *
	 */
	public function rec_listOp(){
		$model = model('rec_position');
		//删除推荐位
		if (chksubmit()){
			$condition = array('rec_id'=>array('in',$_POST['rec_id']));
			$list = $model->where($condition)->select();
			if (!$list)	showMessage(Language::get('param_error'));

			$result = $model->where($condition)->delete();
			if ($result){
				foreach ($list as $info){
					$info['content'] = unserialize($info['content']);
					if ($info['pic_type'] == 1 && is_array($info['content']['body'])){
						foreach ($info['content']['body'] as $v){
							$file = BASE_UPLOAD_PATH.'/'.$v['title'];
							if (is_file($file)) @unlink($file);
						}
					}
					dkcache("rec_position/{$info['rec_id']}");
				}
				$this->log(L('nc_del,rec_position').'['.implode(',',$_POST['rec_id']).']',1);
			}else{
				showMessage(Language::get('nc_common_del_fail'));
			}
		}
		$condition = array();
		if ($_GET['pic_type'] == '0'){
			$condition['pic_type'] = 0;
		}elseif($_GET['pic_type'] == 1){
			$condition['pic_type'] = array('in','1,2');
		}
		if (!empty($_GET['keywords'])){
			$condition['title'] = array('like','%'.$_GET['keywords'].'%');
		}
		$list = $model->where($condition)->order('rec_id desc')->page(10)->select();
		foreach ((array)$list as $k=>$v){
			$list[$k]['content'] = unserialize($v['content']);
			if ($v['pic_type'] == 1){
				$list[$k]['content']['body'][0]['title'] = UPLOAD_SITE_URL.'/'.$list[$k]['content']['body'][0]['title'];
			}
		}
		Tpl::output('list',$list);
		Tpl::output('page',$model->showpage());
		Tpl::showpage('rec_position.index');
	}

	/**
	 * 新增推荐位
	 *
	 */
	public function rec_addOp(){
		Tpl::showpage('rec_position.add');
	}

	/**
	 * 编辑推荐位
	 *
	 */
	public function rec_editOp(){
		$model = Model('rec_position');
		$info = $model->where(array('rec_id'=>intval($_GET['rec_id'])))->find();
		if (!$info)	showMessage(Language::get('no_record'));
		$info['content'] = unserialize($info['content']);
		foreach((array)$info['content']['body'] as $k=>$v){
			if ($info['pic_type'] == 1){
				$info['content']['body'][$k]['title'] = UPLOAD_SITE_URL.'/'.$v['title'];
			}
		}
		Tpl::output('info',$info);
		Tpl::showpage('rec_position.edit');
	}

	/**
	 * 删除
	 *
	 */
	public function rec_delOp(){
		$model = Model('rec_position');
		$_GET['rec_id'] = intval($_GET['rec_id']);
		$info = $model->where(array('rec_id'=>$_GET['rec_id']))->find();
		if (!$info)	showMessage(Language::get('no_record'));

		$info['content'] = unserialize($info['content']);
		$result = $model->where(array('rec_id'=>$_GET['rec_id']))->delete();
		if ($result){
			if ($info['pic_type'] == 1 && is_array($info['content']['body'])){
				foreach ($info['content']['body'] as $v){
					@unlink(BASE_UPLOAD_PATH.'/'.$v['title']);
				}
			}
			dkcache("rec_position/{$info['rec_id']}");
			$this->log(L('nc_del,rec_position').'[ID:'.$_GET['rec_id'].']',1);
			showMessage(Language::get('nc_common_save_succ'));
		}else{
			showMessage(Language::get('nc_common_save_fail'));
		}
	}

	/**
	 * 添加保存推荐位
	 *
	 */
	public function rec_saveOp(){
		$array = array();
		$data = array();
		$pattern = "/^http:\/\/[A-Za-z0-9]+[A-Za-z0-9.]+\.[A-Za-z0-9]+/i";
		//文字类型
		if ($_POST['rec_type'] == 1){
			if (is_array($_POST['txt']) && is_array($_POST['urltxt'])){
				foreach ($_POST['txt'] as $k=>$v){
					if (trim($v) == '') continue;
					$c = count($array['body']);
					$array['body'][$c]['title'] = $v;
					$array['body'][$c]['url'] = preg_match($pattern,$_POST['urltxt'][$k]) ? $_POST['urltxt'][$k] : '';
					$data['pic_type'] = 0;
				}
			}else{
				showMessage(Language::get('param_error'));
			}
		}elseif ($_POST['rec_type'] == 2 && $_POST['pic_type'] == 1){
			//本地图片上传
			if (is_array($_FILES['pic']['tmp_name'])){
				foreach($_FILES['pic']['tmp_name'] as $k=>$v){
					if (empty($v)) continue;
					$ext = strtolower(pathinfo($_FILES['pic']['name'][$k], PATHINFO_EXTENSION));
					if (in_array($ext,array('jpg','jpeg','gif','png'))){
						$filename = substr(md5(microtime(true)),0,16).rand(100,999).$k.'.'.$ext;
						if ($_FILES['pic']['size'][$k]<1024*1024){
							move_uploaded_file($v,BASE_UPLOAD_PATH.'/'.ATTACH_REC_POSITION.'/'.$filename);
						}
						if ($_FILES['pic']['error'][$k] != 0) showMessage(Language::get('nc_common_op_fail'));
						$c = count($array['body']);
						$array['body'][$c]['title'] = ATTACH_REC_POSITION.'/'.$filename;
						$array['body'][$c]['url'] 	= preg_match($pattern,$_POST['urlup'][$k]) ? $_POST['urlup'][$k] : '';
						$array['width']				= is_numeric($_POST['rwidth']) ? $_POST['rwidth'] : '';
						$array['height']			= is_numeric($_POST['rheight']) ? $_POST['rheight'] : '';
						$data['pic_type']			= 1;
					}
					if (empty($array)) showMessage(Language::get('param_error'));
				}
			}
		}elseif ($_POST['rec_type'] == 2 && $_POST['pic_type'] == 2){

			//远程图片
			if (is_array($_POST['pic'])){
				foreach ($_POST['pic'] as $k=>$v){
					if (!preg_match("/^(http\:\/\/)/i",$v)) continue;
					$ext = strtolower(pathinfo($v, PATHINFO_EXTENSION));
					if (in_array($ext,array('jpg','jpeg','gif','png','bmp'))){
						$c = count($array['body']);
						$array['body'][$c]['title'] = $v;
						$array['body'][$c]['url'] 	= preg_match($pattern,$_POST['urlremote'][$k]) ? $_POST['urlremote'][$k] : '';
						$array['width']				= is_numeric($_POST['rwidth']) ? $_POST['rwidth'] : '';
						$array['height']			= is_numeric($_POST['rheight']) ? $_POST['rheight'] : '';
						$data['pic_type']			= 2;
					}
					if (empty($array)) showMessage(Language::get('param_error'));
				}
			}
		}else{
			//showMessage(Language::get('param_error'));
		}
                
                if($_POST['pic_type']==3){
                    $data['rec_app'] = 1;
                    $data['pic_width'] 		= $_POST['rwidth'];
                    $data['pic_height'] 	= $_POST['rheight'];
                }
                
                $data['rec_state'] 		= $_POST['rec_state'];
                $data['rec_start_time'] 	= strtotime($_POST['stime']);
                $data['rec_stop_time'] 		= strtotime($_POST['etime']);
            
		$array['target'] 	= intval($_POST['rtarget']);
		$data['title'] 		= $_POST['rtitle'];
		$data['content'] 	= serialize($array);
		$model = Model('rec_position');
		$model->insert($data);
		$this->log(L('nc_add,rec_position').'['.$_POST['rtitle'].']',1);
		showMessage(Language::get('nc_common_save_succ'),'index.php?act=rec_position&op=rec_list');
	}

	/**
	 * 编辑保存推荐位
	 *
	 */
	public function rec_edit_saveOp(){
		if (!is_numeric($_POST['rec_id'])) showMessage(Language::get('param_error'));
		$array = array();
		$data = array();
		$pattern = "/^http:\/\/[A-Za-z0-9]+[A-Za-z0-9.]+\.[A-Za-z0-9]+/i";
		//文字类型
		if ($_POST['rec_type'] == 1){
			if (is_array($_POST['txt']) && is_array($_POST['urltxt'])){
				foreach ($_POST['txt'] as $k=>$v){
					if (trim($v) == '') continue;
					$c = count($array['body']);
					$array['body'][$c]['title'] = $v;
					$array['body'][$c]['url'] = preg_match($pattern,$_POST['urltxt'][$k]) ? $_POST['urltxt'][$k] : '';
					$data['pic_type'] = 0;
				}
			}else{
				showMessage(Language::get('param_error'));
			}
		}elseif ($_POST['rec_type'] == 2 && $_POST['pic_type'] == 1){
			//本地图片上传
			if (is_array($_FILES['pic']['tmp_name'])){
				foreach($_FILES['pic']['tmp_name'] as $k=>$v){
					//未上传新图的，还用老图
					if (empty($v) && !empty($_POST['opic'][$k])){
						$array['body'][$k]['title'] = str_ireplace(UPLOAD_SITE_URL.'/','',$_POST['opic'][$k]);
						$array['body'][$k]['url'] 	= preg_match($pattern,$_POST['urlup'][$k]) ? $_POST['urlup'][$k] : '';
					}
					$ext = strtolower(pathinfo($_FILES['pic']['name'][$k], PATHINFO_EXTENSION));
					if (in_array($ext,array('jpg','jpeg','gif','png','bmp'))){
						$filename = substr(md5(microtime(true)),0,16).rand(100,999).$k.'.'.$ext;
						if ($_FILES['pic']['size'][$k]<1024*1024){
							move_uploaded_file($v,BASE_UPLOAD_PATH.'/'.ATTACH_REC_POSITION.'/'.$filename);
						}
						if ($_FILES['pic']['error'][$k] != 0) showMessage(Language::get('nc_common_save_fail'));

						//删除老图
						$old_file = str_ireplace(array(UPLOAD_SITE_URL,'..'),array(BASE_UPLOAD_PATH,''),$_POST['opic'][$k]);
						if (is_file($old_file)) @unlink($old_file);

						$array['body'][$k]['title'] = ATTACH_REC_POSITION.'/'.$filename;
						$array['body'][$k]['url'] 	= preg_match($pattern,$_POST['urlup'][$k]) ? $_POST['urlup'][$k] : '';
						$data['pic_type']			= 1;
					}
				}

				//最后删除数据库里有但没有POST过来的图片
				$model = Model('rec_position');
				$oinfo = $model->where(array('rec_id'=>$_POST['rec_id']))->find();
				$oinfo = unserialize($oinfo['content']);
				foreach ($oinfo['body'] as $k=>$v) {
					if (!in_array(UPLOAD_SITE_URL.'/'.$v['title'],(array)$_POST['opic'])){
						if (is_file(BASE_UPLOAD_PATH.'/'.$v['title'])){
							@unlink(BASE_UPLOAD_PATH.'/'.$v['title']);
						}
					}
				}
				unset($oinfo);
			}
			//如果是上传图片，则取原图片地址
			if (empty($array)){
				if (is_array($_POST['opic'])){
					foreach ($_POST['opic'] as $k=>$v){
						$array['body'][$k]['title'] = $v;
						$array['body'][$k]['url'] 	= preg_match($pattern,$_POST['urlup'][$k]) ? $_POST['urlup'][$k] : '';
					}
				}
			}
		}elseif ($_POST['rec_type'] == 2 && $_POST['pic_type'] == 2){

			//远程图片
			if (is_array($_POST['pic'])){
				foreach ($_POST['pic'] as $k=>$v){
					if (!preg_match("/^(http\:\/\/)/i",$v)) continue;
					$ext = strtolower(pathinfo($v, PATHINFO_EXTENSION));
					if (in_array($ext,array('jpg','jpeg','gif','png','bmp'))){
						$c = count($array['body']);
						$array['body'][$c]['title'] = $v;
						$array['body'][$c]['url'] 	= preg_match($pattern,$_POST['urlremote'][$k]) ? $_POST['urlremote'][$k] : '';
						$data['pic_type']			= 2;
					}
				}
			}
		}else{
			//showMessage(Language::get('param_error'));
		}

		if ($_POST['rec_type'] != 1){
			$array['width']				= is_numeric($_POST['rwidth']) ? $_POST['rwidth'] : '';
			$array['height']			= is_numeric($_POST['rheight']) ? $_POST['rheight'] : '';
		}
                
                 if($_POST['pic_type']==3){
                    $data['rec_app'] = 1;
                    $data['pic_width'] 		= $_POST['rwidth'];
                    $data['pic_height'] 	= $_POST['rheight'];
                }
                
                $data['rec_state'] 		= $_POST['rec_state'];
                $data['rec_start_time'] 	= strtotime($_POST['stime']);
                $data['rec_stop_time']         = strtotime($_POST['etime']);

		$array['target'] 	= intval($_POST['rtarget']);
		$data['title'] 		= $_POST['rtitle'];
		$data['content'] 	= serialize($array);

		$model = Model('rec_position');

		//如果是把本地上传类型改为文字或远程，则先取出原来上传的图片路径，待update成功后，再删除这些图片
		if ($_POST['opic_type'] == 1 && ($_POST['pic_type'] == 2 || $_POST['rec_type'] == 1)){
			$oinfo = $model->where(array('rec_id'=>$_POST['rec_id']))->find();
			$oinfo = unserialize($oinfo['content']);
		}
		$result = $model->where(array('rec_id'=>$_POST['rec_id']))->update($data);
		if ($result){
			if ($oinfo){
				foreach ($oinfo['body'] as $v){
					if (is_file(BASE_UPLOAD_PATH.'/'.$v['title'])){
						@unlink(BASE_UPLOAD_PATH.'/'.$v['title']);
					}
				}
			}

			dkcache("rec_position/{$_POST['rec_id']}");
			showMessage(Language::get('nc_common_save_succ'),'index.php?act=rec_position&op=rec_list');
		}else{
			showMessage(Language::get('nc_common_save_fail'),'index.php?act=rec_position&op=rec_list');
		}
	}

	public function rec_codeOp(){
		Tpl::showpage('rec_position.code','null_layout');
	}

	public function rec_viewOp(){
		@header("Content-type: text/html; charset=".CHARSET);
		echo rec(intval($_GET['rec_id']));
	}
        
	/**
	 * 异步修改
	 */
	public function ajaxOp(){
		if(in_array($_GET['branch'],array('activity_title','activity_sort'))){
			$activity = Model('activity');
			$update_array = array();
			switch ($_GET['branch']){
				/**
				 * 活动主题
				 */
				case 'activity_title':
					if(trim($_GET['value'])=='')exit;
					break;
				/**
				 * 排序
				 */
				case 'activity_sort':
					if(preg_match('/^\d+$/',trim($_GET['value']))<=0 or intval(trim($_GET['value']))<0 or intval(trim($_GET['value']))>255)exit;
					break;
				default:
						exit;
			}
			$update_array[$_GET['column']] = trim($_GET['value']);
			if($activity->update($update_array,intval($_GET['id'])))
			echo 'true';
		}elseif(in_array($_GET['branch'],array('rec_detail_sort'))){
			$rec_applys_detail = Model('rec_applys');
			$update_array = array();
			switch ($_GET['branch']){
				/**
				 * 排序
				 */
				case 'rec_detail_sort':
					if(preg_match('/^\d+$/',trim($_GET['value']))<=0 or intval(trim($_GET['value']))<0 or intval(trim($_GET['value']))>255)exit;
					break;
				default:
						exit;
			}
			$update_array[$_GET['column']] = trim($_GET['value']);
			if($rec_applys_detail->update($update_array,intval($_GET['id'])))
			echo 'true';
		}
	}
    
}
