<?php

/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-3
 * Time: 上午11:00
 */
namespace Home\Service;

use think\model;

class DepartmentService {
	/**
	 * 根据上级部门获取部门
	 */
	public static function getDepartment(int $parent_id) {
		return M ( "Department" )->field ( "departmentid as id,name,isparent,parentid" )->where ( "disabled='0' and departmentid>1 and parentid=" . $parent_id )->order ( 'listorder asc,departmentid asc' )->select ();
	}
	
	/**
	 * 新增部门
	 */
	public static function addDepartmentRelation($data = array()) {
		if (count ( $data )) {
			$model = new Model ();
			$model->startTrans ();
			$dep = M ( "Department" );
			$flag = true;
			// 新增部门
			$last_deparment_id = $dep->add ( $data );
			if (! $last_deparment_id) {
				$model->rollback ();
				$flag = false;
			}
			// 更新path
			$p_detail = $dep->where ( "departmentid='" . $data ['parentid'] . "'" )->find ();
			$s_data ['path'] = $p_detail ['path'] . '-' . $last_deparment_id;
			$res2 = $dep->where ( "departmentid='" . $last_deparment_id . "'" )->save ( $s_data );
			if (! $res2) {
				$model->rollback ();
				$flag = false;
			}
			// 更新上级id
			$p_data ['isparent'] = 1;
			$res3 = $dep->where ( "departmentid='" . $data ['parentid'] . "'" )->save ( $p_data );
			$model->commit ();
			return ($flag ? $last_deparment_id : false);
		}
	}
	
	/**
	 * 删除部门
	 */
	public static function removeDepartmentByDid($did) {
		$dep = M ( "Department" );
		$condition ['departmentid'] = $did;
		$data ['disabled'] = 1;
		$condition_p ['parentid'] = $did;
		$res_1 = $dep->where ( $condition )->save ( $data );
		$res_2 = $dep->where ( $condition_p )->save ( $data );
		return true;
	}
	/**
	 * 重新命名部门
	 */
	public static function renameDepartmentByDid($did, $name) {
		$dep = M ( "Department" );
		$condition ['departmentid'] = $did;
		$data ['name'] = $name;
		$res_1 = $dep->where ( $condition )->save ( $data );
		return true;
	}
}