<?php
/*
 * 首页方法
 *
 */
namespace Home\Controller;

use Think\Controller;
use Home\Service\CustomerService;
use Home\Service\UserService;

//首页
class IndexController extends BaseController {
	
	protected $customer_arr = array();
	function _initialize() {
		
		parent::_initialize ();
		$this->show_index();
	}
	//获取首页信息
	function show_index(){
		$is_admin = ($this->user_info['roleid']==1) ?true:false;
		if($is_admin)
		{
			$where = " company_status='APPROVE' ";
		}else{
			$user_arr =  UserService::getUserAndFollowCustomerIds($this->user_info['userid']);
			$user_ids =  implode(',',$user_arr);
			$where = " company_status='APPROVE' and crm_provider_user_id in (".$user_ids.")";
		}
		$this->customer_arr['approve']= M('Member_base')->where($where)->count();
		$this->assign('customer_arr',$this->customer_arr);
	}
}




