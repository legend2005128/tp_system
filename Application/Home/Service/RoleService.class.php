<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-3
 * Time: 上午11:00
 */

namespace Home\Service;

use think\model;

class RoleService
{
    /**
     * 获取用户权限组
     */
    public  static  function getRoleGroup(  )
    {
        return M("Role")->select();
    }
    /**
     * 获取用户权限
     */
    public  static  function getRoleByUserid( $uid )
    {
        $condition['userid'] = $uid;
        $Model = M('Users');
        $arr = $Model->field(C('db_prefix').'role'.'.roleid,rolename')->join('__ROLE__ ON __USERS__.	roleid = __ROLE__.roleid')->where($condition)->find();
        return $arr;
    }

    /**
     * 获取权限
     */
    public  static  function getRoleByrid( $rid )
    {
        $condition['roleid'] = $rid;
        $Model = M('Role');
        $arr = $Model->field('rolename')->where($condition)->find();
        return $arr;
    }
    /**
     * 获取角色菜单id
     */
    public static function getMenuIdByRid($rid)
    {
        $Model = M('Role_menu');
        $condition['roleid'] = $rid;
        return $Model->field('menuid')->where($condition)->select();
    }
}