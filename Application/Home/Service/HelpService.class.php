<?php
/**
 * 用户认证服务
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-21
 * Time: 下午2:12
 */
namespace Home\Service;

use Think\Model;

class HelpService {
/**
 * 获取当前应用的域名
 *
 */
static public function get_host(){
	$ret_arr = array();
	if( I('server.REDIRECT_STATUS') == 200)
	{
		$cur_host = I('server.SERVER_NAME');
		$domain = substr($cur_host,stripos($cur_host,'.'));
		$ret_arr['COOKIE_DOMAIN'] = $domain;
		$ret_arr['HOST'][ 'THIS_HOST']= I('server.HTTP_HOST') ? 'http://'.I('server.HTTP_HOST') : '';
	}

	return $ret_arr;
}

/**
 * 设置config
 * 支持tp<2级配置
 *
 */
static public function set_host( $config = array() ){
	if(is_array($config)){
		foreach ($config as $ke =>$va ){
			if(is_array($va)){
				foreach ($va as $k=>$v){
					C($ke.".".$k,$v);
				}
			}else{
				C($ke,$va);
			}
		}
	}
}
}