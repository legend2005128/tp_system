<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-24
 * Time: 下午2:57
 */
namespace Home\Controller;
use Home\Service\MenuService;
use Home\Service\RoleService;
use Think\Controller;

//用户角色
class RoleController extends BaseController {

    function _initialize(){
        parent::_initialize();
    }
    /***
     * 角色列表
     *
     */
    public function list()
    {
        if(IS_AJAX){
           $this->_ajax_list();
        }else{
            $this->render('role/list');
        }
    }

    /***
     *角色列表
     *
     */
    protected function _ajax_list()
    {
        $role_m = M("Role");
        $role_info = $role_m->where("disabled='0' ")->order('roleid asc')->select();
        foreach ($role_info as $keys=>$item)
        {
            $url = U('role/edit')."/rid/".$item['roleid'];
            $setmenu_url = U('role/menuset')."/rid/".$item['roleid'];
            if( $item['status'] == 1){
                $role_info[$keys]['operation'] = '<a class="like J-edt" href="'.$url.'" title="编辑" data-id="'.$item['roleid'].'">编辑</a> | <a class="like J-menu-set" href="'.$setmenu_url.'" title="设置权限" >设置权限</a>';
            }else{
                $role_info[$keys]['operation'] = '<a class="like J-edt" href="'.$url.'" title="编辑" data-id="'.$item['roleid'].'">编辑</a> | <a class="like J-menu-set" href="'.$setmenu_url.'" title="设置权限" >设置权限</a>';
            }
        }
        echo json_encode($role_info);
    }
    /***
     * 新增角色
     *
     */
    public function add()
    {
        if(IS_POST)
        {
            $rules = array(
                array('rolename','require','角色名称为空'),
            );
            $role = M("Role");
            if (!$role->validate($rules)->create()){
                $error = $role->getError();
                $this->assign('error',$error);
                $this->render('role/add');
                exit;
            }else{

                $data['rolename'] = I('post.rolename','','trim');
                $data['description'] =  I('post.description','','trim');
                $re= $role->add($data);
                if($re)
                {
                    $this->success('新增成功','/role/list',2);
                }else{
                    $this->error('新增失败','/role/list',2);
                }
            }
        }else{

            $this->render('role/add');
        }
    }

    /**
     * 角色删除
     *
     */
     public function dels()
     {
         if(!IS_AJAX) {
             $this->error('请求失败','/role/list',2);
         }
         $ids = I('post.ids','');
         if(!$ids){
             $data = array(
                 'code'=> 1002,
                 'msg' => '参数错误'
             );
             $this->ajaxReturn($data,'json');
         }
         $role = M('Role');
         $ids = trim($ids,',');
         $cdata['disabled'] = 1;
         $res = true;
         $id_arr = explode(',',$ids);
         foreach ($id_arr as $uid) {
             $res = $res &&( $role->where( 'roleid='.intval($uid) )->save($cdata)); // 根据条件更新记录
         }
         if($res)
         {
             $data = array(
                 'code'=> 1001,
                 'msg' => '操作成功'
             );
             $this->ajaxReturn($data,'json');
         }else{
             $data = array(
                 'code'=> 1003,
                 'msg' => '操作失败'
             );
             $this->ajaxReturn($data,'json');
         }
     }
    //编辑角色
    public function edit()
    {
        $rid = I('get.rid','');
        $role = M("Role");
        $roleinfo = $role->where("roleid='".$rid."'")->find();
        $this->assign('roleinfo',$roleinfo);
        if(IS_POST)
        {
            $rules = array(
                array('rolename','require','角色名称为空'),
            );
            $role = M("Role");
            if (!$role->validate($rules)->create()){
                $error = $role->getError();
                $this->assign('error',$error);
                $this->render('role/add');
                exit;
            }else{
                $data['rolename'] = I('post.rolename','','trim');
                $data['description'] =  I('post.description','','trim');
                $re = $role->where( 'roleid='.$rid )->save($data) ; // 根据条件更新记录
                if($re)
                {
                    $this->success('编辑成功','/role/list',2);
                }else{
                    $this->error('编辑失败','/role/list',2);
                }
            }
        }else{

            $this->render('role/edit');
        }
    }

    /***
     * 菜单设置
     *
     */
    public function menuset()
    {

        if (IS_POST) {
            $menuid_arr = I('post.menuid', '', 'trim');
            $rid = I('post.rid', 0, 'intval');
            if (is_array($menuid_arr) && count($menuid_arr)) {
                $re = MenuService::addRoleMenuid($rid, $menuid_arr);
                if ($re) {
                    $this->success('编辑成功', '/role/list', 2);
                } else {
                    $this->error('编辑失败', '/role/list', 2);
                }
            }

        } else {
            $rid = I('get.rid', 0,'intval');
            if($rid){
                $rolearr = RoleService::getRoleByrid($rid);
                $this->assign('rolename', $rolearr['rolename']);
            }
            $this->assign('roleid', $rid);
            $menuList = MenuService::getTreemenu();
            $this->assign('menulist', $menuList);
            $u_menuList = MenuService::getMenuByrid($rid);
            foreach ($u_menuList as $i) {
                $u_menu_arr[] = $i['menuid'];
            }
            $this->assign('u_menu_arr', $u_menu_arr);
            $this->render('role/menuset');
        }
    }

}