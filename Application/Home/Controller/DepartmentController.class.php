<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-24
 * Time: 下午2:57
 */
namespace Home\Controller;
use Home\Service\DepartmentService;
use Home\Service\GroupService;
use Home\Service\MenuService;
use Home\Service\RoleService;
use Think\Controller;
use Home\Service\UserService;

//部门控制器
class DepartmentController extends BaseController {

    function _initialize(){
        parent::_initialize();
    }
    /***
     * 部门首页
     *
     */
    public function list()
    {

        $this->render('department/list');
    }

    /**
     *
     * 部门数据ztree
     */
    public function ajax_getdata(){
        if(!IS_AJAX)
        {
            $this->ajaxReturn([],'json');
        }
        $parent_id = I('post.id',1,'intval');
        $user_id = I('post.user_id','','intval');
        $depart_arr = [];
        $depart_arr = DepartmentService::getDepartment( $parent_id );
        //编辑用户使用
        if($user_id)
        {
	        $depart_arrs = UserService::getUserDepartmentid($user_id);
	        $tmp_arr = array();
	        foreach ($depart_arrs as $v)
	        {
	        	$tmp_arr[] = $v['department_id'];
	        }
        }
        foreach ($depart_arr as $k=>$v)
        {
            $depart_arr[$k]['isParent'] = $v['isparent']?true:false;
            if($user_id)
            {  	
            	if(in_array($v['id'],$tmp_arr))
            	{
            	   $depart_arr[$k]['checked'] = true;
            	}else{
            		unset($depart_arr[$k]['checked']) ;
            	}
            }
            
        }

        $this->ajaxReturn($depart_arr,'json');
    }
    /***
     * 新增部门
     *
     */
    public function ajax_add()
    {
          $data['parentid'] = I('post.pid','','intval');
          $data['name']  = I('post.name','','trim');
          $department_id =  DepartmentService::addDepartmentRelation($data);
          $data = array(
                 'code'=> 1001,
                 'msg' => 'success',
                 'data'=>$department_id
             );
             $this->ajaxReturn($data,'json');
    }

    /**
     *delete deparment
     *
     */
     public function ajax_dels()
     {
         if(!IS_AJAX) {
             $this->error('请求失败','/deparment/list',2);
         }
         $id = I('post.dep_id','','intval');
         if(!$id){
             $data = array(
                 'code'=> 1002,
                 'msg' => '参数错误'
             );
             $this->ajaxReturn($data,'json');
         }
        $res = DepartmentService::removeDepartmentByDid($id);
         if($res)
         {
             $data = array(
                 'code'=> 1001,
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

     /**
     *delete deparment
     *
     */
     public function ajax_rename()
     {
         if(!IS_AJAX) {
             $this->error('请求失败','/deparment/list',2);
         }
         $id = I('post.dep_id','','intval');
          $name = I('post.name','','trim');
         if(!$id){
             $data = array(
                 'code'=> 1002,
                 'msg' => '参数错误'
             );
             $this->ajaxReturn($data,'json');
         }
        $res = DepartmentService::renameDepartmentByDid($id,$name);
         if($res)
         {
             $data = array(
                 'code'=> 1001,
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
   

}