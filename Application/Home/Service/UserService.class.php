<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-7
 * Time: 下午4:43
 */

namespace Home\Service;


class UserService 
{
    public static function listAll($where,$field)
    {
        $User = M("Users");
        return $userlist = $User->field($field)->where("status=1  ".$where)->select();
    }

    //获取用户 下面的 客户id
    public static function  getUserAndFollowCustomerIds( $userid )
    {
        $department_user_model = M('Department_user');
        $department_arr = $department_user_model->where("user_id='".$userid."'")->select();
        $user_list = array();
         array_push( $user_list , $userid);
        foreach ($department_arr as $item )
        {
            $department_user_model = M('Department_user');
            $children_user_arr= $department_user_model->where("department_id='".$item['department_id']."'")->select();
            foreach ($children_user_arr as $it)
            {
                array_push( $user_list , intval($it['user_id']));
            }
        }
        $user_list = array_unique($user_list);
        return $user_list;
    }
    //获取用户的部门ids
    public  static function getUserDepartmentid( $user_id )
    {
    	return M('Department_user')->field('department_id')->where('user_id='.$user_id)->select();
    }
}