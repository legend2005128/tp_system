<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-8
 * Time: 下午2:56
 */

namespace Home\Service;
use think\model;

class MenuService
{

    //获取顶级菜单
    public static function getAllTopMenu()
    {
        return M("Menu")->where("display='1' and parentid=0")->order('listorder asc')->select();
    }
    //获取parentid所有菜单
    public static function getMenu($parentid)
    {
        return M("Menu")->where("display='1' and parentid='".$parentid."'")->order('listorder asc')->select();
    }

    //获取user所有菜单
    public static function getMenuByrid($rid)
    {
        return M("Role_menu")->field(''.C('db_prefix').'menu.menuid as menuid,parentid')->join('__MENU__ ON  __ROLE_MENU__.menuid = __MENU__.menuid')->where("roleid='".$rid."'")->select();
    }

    //设置菜单菜单
    public static function addRoleMenuid( $roleid,$menu_arr =array())
    {
        foreach($menu_arr as $v)
        {
            $dataList[] = array('roleid'=>$roleid,'menuid'=>$v);
        }
        M("Role_menu")->where("roleid='".$roleid."'")->delete();
        return M("Role_menu")->addAll($dataList);
    }
    //获取菜单详情
    public static function getMenuByCondition($where)
    {
    	return M("Menu")->where($where)->find();
    }
    
    //获取所有菜单
    public static function getAllMenus()
    {
    	return M("Menu")->field("*,if(display,'显示','隐藏') as display")->order('listorder asc')->select();
    }
    //删除菜单
    public static function delMenubyMid($mid )
    {
    	return M("Menu")->where("menuid='".$mid."'")->delete();
    }
    //新增菜单
    public static function addMenus($data )
    {
    	
    	
    	$lid = M("Menu")->add($data);
    	$row = M("Menu")->where("menuid='".$data['parentid']."'")->find();
    	$p_data['path'] = $row['path'].'-'.$lid;
    	
    	return $res3 = M("Menu")->where ( "menuid='" . $lid . "'" )->save ( $p_data );
    	
    }
    //获取树型菜单
    public static function getTreemenu(){
    		$res = M("Menu")->order('path asc')->select();
    		foreach ($res as $k=>$it)
    		{
    			$count = count(explode('-',$it['path']));
    			$res[$k]['place'] = str_repeat('⊥', $count);
    		}
    		return $res;
    }
    
    
}