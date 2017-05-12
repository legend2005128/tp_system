<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-21
 * Time: 下午2:36
 */

namespace Home\Controller;


use Home\Service\AuthService;

use Home\Service\MenuService;
use Home\Service\MessageService;
use Think\Controller;
use Home\Service\RoleService;
use Home\Service\HelpService;

class BaseController extends Controller
{
   		public $user_login_status ;
        public  $user_info;
		protected $_returnUrl = '';
        function _initialize()
        {
        	$this->set_hosts();
        	if( !$this->_returnUrl )
        	{
        		$this->_returnUrl = I('server.REQUEST_URI');
        	}
  
            $this->chk_login(); //检测登录
       		//权限
       		$this->check_access();   
        }

        /***
         * 检测登录
         */
        public function chk_login()
        {
        	if( !cookie('crm_uuid') )
        	{
        		$this->error('请先登录','/login?ref='.$this->_returnUrl,1);
        	}
        	$this->user_info = S('crm_'.cookie('crm_uuid'));//获取缓存基本信息
            if(!$this->user_info)
            {
            	$this->user_info = AuthService::getUserAuthInfo();//获取登录用户信息
            	S('crm_'.cookie('crm_uuid'),$this->user_info,7200);//缓存基本信息
            }
            
            $this->user_login_status =1;
            $messNoRead=MessageService::getNoread($this->user_info['userid']);
            $messNoNum = $messNoRead?count($messNoRead):0;
            $this->assign('messnolist',$messNoRead);
            $this->assign('messnonum',$messNoNum);
            $this->assign('us',$this->user_info);
        }

        /**
         * render view
         *
         */
        public function render( $view,$data = array())
        {
            $this->display('Ini/header');
            if($data && count($data))
            {
                foreach ($data as $key => $value) 
                {
                    $this->assign($key,$value);
                }
            }
            $this->display( ucfirst($view) );
            $this->display('Ini/bottom');
        }
        
        /**
         * 
         * 检测 角色-菜单权限
         * 
         */
        protected function check_access()
        {
        
         	//获取当前菜单id
        	$uri = I('server.REQUEST_URI');
        	$condition = [];
       		$condition['url'] = str_replace('/', '-', trim($uri,'/'));
         	 $menus = MenuService::getMenuByCondition($condition);
         	 $menuid = $menus['menuid'];
        	//获取用户菜单id组
			$cur_role_id = $this->user_info['roleid'];
		
         	$cur_menuid_arr = RoleService::getMenuIdByRid($cur_role_id);
         	
         	$flag = false;
         	foreach($cur_menuid_arr as $item)
         	{
         		if($menuid == $item['menuid'])
         		{
         			$flag= true;
         			break;
         		}
         	}
         	if($cur_role_id !== 1){
	         	//判断用户是否可以操作该操作
	         	if($flag === false && $uri!='/')
	         	{
	         		Msgs( '您没有操作权限',U('/'),2);
	         		return ;
	         	}
         	}
        }
        /**
         * 设置cookie作用域和静态host
         */
        public function set_hosts($param = array())
        {
        	if (!C('HOST.THIS_HOST') || !C('HOST.COOKIE_DOMAIN')) {
        		$host_arr = HelpService::get_host();
        		HelpService::set_host($host_arr);
        	}
        }

}