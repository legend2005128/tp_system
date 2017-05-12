<?php
namespace Home\Widget;

use Think\Controller;

class MenuWidget extends Controller{

    public static $menuList;
    public static $menu_role_arrs;
	  public function lists($rid)
      {
          if(!self::$menuList){
              $menu_role_arrs = array();
              $menu_role_arr = \Home\Service\MenuService::getMenuByrid($rid);
              foreach( $menu_role_arr as $ik=>$iv )
              {
                  $menu_role_arrs[] = intval($iv['menuid']);
                  if($iv['parentid']){
                      $menu_role_arrs[] = intval($iv['parentid']);
                  }
              }
              $menuList = array();
              $menuList = \Home\Service\MenuService::getAllTopMenu();
              foreach ($menuList as $key => $item) {

                  if(($menu_role_arrs)&&(!in_array($item['menuid'],$menu_role_arrs))&&(!in_array($item['parentid'],$menu_role_arrs)) )
                  {
                      unset($menuList[$key]);
                      continue;
                  }
                  $menuList[$key]['children'] = \Home\Service\MenuService::getMenu($item['menuid']);
              }
              self::$menuList = $menuList;
              self::$menu_role_arrs = $menu_role_arrs;
          }
          $this->assign('menuList',self::$menuList);
          $this->assign('menu_role_arrs',self::$menu_role_arrs);
          $this->display('Ini:menu');
      }
}