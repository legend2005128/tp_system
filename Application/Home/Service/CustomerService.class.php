<?php

/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-15
 * Time: 上午8:59
 */
namespace Home\Service;

class CustomerService {
	// 根据服务商id获取客户
	public static function listCustomerBycondition($field, $uid) {
		$User = M ( "Member_base" );
		if (is_numeric ( $uid )) {
			return $User->field ( $field )->where ( "status=1  and crm_provider_user_id=" . $uid )->select ();
		} elseif (strpos ( $uid, ',' ) !== false) {
			return $User->field ( $field )->where ( "status=1  and crm_provider_user_id in " . $uid )->select ();
		}
	}
	//获取所有客户
	public static function getAllCustomers($field, $pagesize, $pagenumer) {
		$User = M ( "Member_base" );
		return $User->field ( $field )->where ( "status=1 " )->page ( $pagenumer, $pagesize )->select ();
	}
	
	// 获取客户详情
	public static function getCustomerDetail($field, $where) {
		$User = M ( "Member_base" );
		return $User->field ( $field )->where ( "status=1 and " . $where )->find ();
	}
	
	// 获取客户跟进列表
	public static function getCustomerGenjinList($where) {
		$User = M ( "Member_genjin" );
		return $User->where ( $where )->order ( 'create_time desc' )->select ();
	}
	
	//获取服务商的符合条件的客户数
	public static function getCustomerCount($where,$isadmin=false)
	{
		if($isadmin)
		{
			unset($where['crm_provider_user_id']);
		}
		return M("Member_base" )->where($where)->count('crm_id');
	}
}