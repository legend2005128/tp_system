<?php

/**
 * 菜单类
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-7
 * Time: 下午4:04
 */
namespace Home\Controller;

use Home\Service\MenuService;

class MenuController extends BaseController {
	function _initialize() {
		parent::_initialize ();
	}
	
	/**
	 * 菜单列表
	 */
	function list() {
		if (IS_AJAX) {
			$this->_ajax_list ();
		} else {
			$this->render ( 'menu/list' );
		}
	}
	/**
	 * *
	 * 菜单列表
	 */
	protected function _ajax_list() {
		exit ( json_encode ( MenuService::getAllMenus () ) );
	}
	
	/**
	 * 菜单获取
	 */
	public function ajax_get_menu() {
		$parent_id = I ( 'post.menuid', '', 'intval' );
		$menu_arr = MenuService::getMenu($parent_id);
		$this->ajaxReturn($menu_arr,'json');
	}
	
	/**
	 * 新增菜单
	 */
	function add() {
		$menu_1 = MenuService::getTreemenu();
		$this->assign ( 'menu_1', $menu_1 );
		
		if (IS_POST) {
			$rules = array (
					array (
							'name',
							'require',
							'菜单名称必填!' 
					),
					array (
							'url',
							'require',
							'地址信息必填!' 
					),
					array (
							'parentid',
							'require',
							'未选择父级菜单' 
					) 
			);
			$menu = M ( "Menu" );
			if (! $menu->validate ( $rules )->create ()) {
				$error = $menu->getError ();
				$this->assign ( 'error', $error );
				$this->render ();
				exit ();
			} else {
				$cdata ['name'] = I ( 'post.name', '', 'trim' );
				$cdata ['parentid'] = I ( 'post.parentid', '', 'intval' );
				$cdata ['url'] = I ( 'post.url', '', 'trim' );
				$cdata ['display'] = I ( 'post.display', '', 'trim' );
				$cdata ['icons'] = I ( 'post.icons', '', 'trim' );
				$re = MenuService::addMenus ( $cdata );
				if ($re) {
					$this->success ( '操作成功', '/menu/list', 1 );
				} else {
					$this->error ( '操作失败', '/menu/list', 1 );
				}
			}
		} else {
			
			$this->render (  );
		}
	}
	
	/**
	 * 菜单删除
	 */
	public function dels() {
		if (! IS_AJAX) {
			$this->error ( '请求失败', '/menu/list', 2 );
		}
		$ids = I ( 'post.ids', '' );
		if (! $ids) {
			$data = array (
					'code' => 1002,
					'msg' => '参数错误' 
			);
			$this->ajaxReturn ( $data, 'json' );
		}
		$ids = trim ( $ids, ',' );
		$id_arr = explode ( ',', $ids );
		
		$res = true;
		foreach ( $id_arr as $mid ) {
			$res = $res && (MenuService::delMenubyMid ( $mid )); // 根据条件更新记录
		}
		if ($res) {
			$data = array (
					'code' => 1001,
					'msg' => '操作成功' 
			);
			$this->ajaxReturn ( $data, 'json' );
		} else {
			$data = array (
					'code' => 1003,
					'msg' => '操作失败' 
			);
			$this->ajaxReturn ( $data, 'json' );
		}
	}
}