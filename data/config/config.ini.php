<?php
// defined('InShopNC') or exit('Access Invalid!');

$config = array();

$config['base_site_url'] 		= 'http://192.168.31.225/33hao';
$config['shop_site_url'] 		= 'http://192.168.31.225/33hao/shop';
$config['cms_site_url'] 		= 'http://192.168.31.225/33hao/cms';
$config['microshop_site_url'] 	= 'http://192.168.31.225/33hao/microshop';
$config['circle_site_url'] 		= 'http://192.168.31.225/33hao/circle';
$config['admin_site_url'] 		= 'http://192.168.31.225/33hao/admin';
$config['mobile_site_url'] 		= 'http://192.168.31.225/33hao/mobile';
$config['wap_site_url'] 		= 'http://192.168.31.225/33hao/wap';
$config['chat_site_url'] 		= 'http://192.168.31.225/33hao/chat';
$config['node_site_url'] 		= 'http://::1:8090';
$config['upload_site_url']		= 'http://192.168.31.225/33hao/data/upload';
$config['resource_site_url']	= 'http://192.168.31.225/33hao/data/resource';

$config['version'] 		= '201505251201';
$config['setup_date'] 	= '2015-09-01 00:39:55';
$config['gip'] 			= 0;
$config['dbdriver'] 	= 'mysqli';
$config['tablepre']		= '33hao_';
$config['db']['1']['dbhost']       = '114.215.82.219';
$config['db']['1']['dbport']       = '3306';
$config['db']['1']['dbuser']       = 'xfe2';
$config['db']['1']['dbpwd']        = 'xfe123456';
$config['db']['1']['dbname']       = '33hao';
$config['db']['1']['dbcharset']    = 'UTF-8';
$config['db']['slave']                  = $config['db']['master'];
$config['session_expire'] 	= 3600;
$config['lang_type'] 		= 'zh_cn';
$config['cookie_pre'] 		= '2310_';
$config['thumb']['cut_type'] = 'gd';
$config['thumb']['impath'] = '';
$config['cache']['type'] 			= 'file';

//友盟接入账号
$config['ad_ym_appkey'] = '56c414f9e0f55a7bba0001ff';
$config['ad_ym_secret']='zdmntjjwn5enyv3s865n1ca8xrdmterc'; 
        
        
//$config['redis']['prefix']      	= 'nc_';
//$config['redis']['master']['port']     	= 6379;
//$config['redis']['master']['host']     	= '127.0.0.1';
//$config['redis']['master']['pconnect'] 	= 0;
//$config['redis']['slave']      	    = array();
//$config['fullindexer']['open']      = false;
//$config['fullindexer']['appname']   = '33hao';
$config['debug'] 			= true;
$config['default_store_id'] = '1';
$config['url_model'] = false;
$config['subdomain_suffix'] = '';
//$config['session_type'] = 'redis';
//$config['session_save_path'] = 'tcp://127.0.0.1:6379';
$config['node_chat'] = true;
//流量记录表数量，为1~10之间的数字，默认为3，数字设置完成后请不要轻易修改，否则可能造成流量统计功能数据错误
$config['flowstat_tablenum'] = 3;
$config['sms']['gwUrl'] = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService';
$config['sms']['serialNumber'] = '';
$config['sms']['password'] = '';
$config['sms']['sessionKey'] = '';
$config['queue']['open'] = false;
$config['queue']['host'] = '127.0.0.1';
$config['queue']['port'] = 6379;
$config['cache_open'] = false;
$config['delivery_site_url']    = 'http://whao.a-caggie.cn/delivery';
return $config;
