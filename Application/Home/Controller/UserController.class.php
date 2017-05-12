<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-24
 * Time: 下午2:57
 */
namespace Home\Controller;
use Home\Service\DepartmentService;
use Home\Service\RoleService;
use Think\Controller;

//用户权限
class UserController extends BaseController {

    function _initialize(){
        parent::_initialize();
    }
  
    
    /***
     * 用户列表
     *
     */
    public function list()
    {
        if(IS_AJAX){
           $this->_ajax_list();
        }else{
            $this->render('user/list');
        }
    }

    /***
     *用户列表
     *
     */
    protected function _ajax_list()
    {

        $User1 = M("Users");
        $user_info = $User1->where("username!='admin'")->order('status desc,userid asc')->select();
        foreach ($user_info as $keys=>$item)
        {
                $role_arr = RoleService::getRoleByUserid($item['userid']);
                $user_info[$keys]['role'] = $role_arr['rolename'];
                if($item['status']){
                    $user_info[$keys]['status'] = '有效';
                }else{
                    $user_info[$keys]['status'] = '无效';
                }
                $user_info[$keys]['lastlogintime'] = $item['lastlogintime']?date('Y-m-d H:i:s',$item['lastlogintime']):'-';
                $url = U('user/edit')."/u_token/".$item['token'];
                if( $item['status'] == 1){
                    $user_info[$keys]['operation'] = '<a class="like J-edt" href="'.$url.'" title="编辑" data-id="'.$item['userid'].'">编辑</a> | <a data-id="'.$item['userid'].'"  href="javascript:void(0)" class="like J-cancle" title="禁用"> 禁用 </a>';
                }else{
                    $user_info[$keys]['operation'] = '<a class="like J-edt" href="'.$url.'" title="编辑" data-id="'.$item['userid'].'">编辑</a> | <a data-id="'.$item['userid'].'" href="javascript:void(0)" class="like J-cancle-off" title="禁用"> 启用 </a>';
                }
                $url_message = U('message/add');
                $user_info[$keys]['operation'] .= ' | <a class="like" href="'.$url_message.'" title="发消息" >发消息</a>';
        }
        echo json_encode($user_info);
    }
    /***
     * 新增
     *
     */
    public function add()
    {
        $role_arr = RoleService::getRoleGroup();
        $this->assign('role_arr',$role_arr);

        if(IS_POST)
        {
            $rules = array(
            array('username','checkUname',L('username_err'),0,'unique',1),
            array('password','checkPwd',L('password_err'),0,'function'),
            array('repassword','password',L('repassword_err'),0,'confirm'),
            array('email','email','email invaild type!'),
                array('deparmentids','require','deparment invaild !'),
            );
            $User = M("Users");
            if (!$User->validate($rules)->create()){
                $error = $User->getError();
                $this->assign('error',$error);
                $this->render('User/add');
                exit;
            }else{

                $data['username'] = I('post.username','','trim');
                $passwd = password( I('post.password','','trim'));
                $data['password'] = $passwd['password'];
                $data['token'] = $passwd['token'];
                $data['email'] = I('post.email','','trim');
                $data['roleid'] = I('post.role','','trim');
                $data['status'] = I('post.status','1','trim');
                $data['addtime'] = time();
                //department
                $deparmentids = trim(I('post.deparmentids','','trim'),',');
                $re= $User->add($data);
                if($re)
                {
                    $deparment_arr = explode(',',$deparmentids);
                    if($deparment_arr)
                    {
                        foreach ($deparment_arr as $it)
                        {
                            $c_data['user_id'] = $re;
                            $c_data['department_id'] = $it;
                        }
                        M("Department_user")->add($c_data);
                    }
                    $this->success('新增成功','/user/list',2);
                }else{
                    $this->error('新增失败','/user/list',2);
                }
            }
        }else{

            $this->render('user/add');
        }
    }

    /**
     * 用户删除
     *
     */
     public function dels()
     {
         if(!IS_AJAX) {
             $this->error('请求失败','/user/list',2);
         }
         $ids = I('post.ids','');
         $type = I('post.type',0,'intval');
         if(!$ids){
             $data = array(
                 'code'=> 1002,
                 'msg' => '参数错误'
             );
             $this->ajaxReturn($data,'json');
         }

         $user = M('users');
         $ids = trim($ids,',');
         $id_arr = explode(',',$ids);
         if($type==0){
             $cdata['status'] = 0;
         }else{
             $cdata['status'] = 1;
         }

         $cdata['updated'] = time();
         $res = true;
         foreach ($id_arr as $uid) {
            $res = $res &&( $user->where( 'userid='.$uid )->save($cdata)); // 根据条件更新记录
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
    //编辑用户
    public function edit()
    {
        //角色
        $role_arr = RoleService::getRoleGroup();
        $this->assign('role_arr',$role_arr);

    
        $user_token = I('get.u_token','');
        $User = M("Users");
        $user_info = $User->where("token='".$user_token."'")->find();
        $this->assign('userinfo',$user_info);
        if(IS_POST)
        {
            $rules = array(
               /*  array('password','checkPwd',L('password_err'),0,'function'),
                array('repassword','password',L('repassword_err'),0,'confirm'), */
                array('email','email','email invaild type!'),
            );
            $User = M("Users");
            if (!$User->validate($rules)->create()){
                $error = $User->getError();
                $this->assign('error',$error);
                $this->render('user/edit');
                exit;
            }else{
                $user_id = I('post.userid','','intval');
                if(I('post.password','','trim') )
                {
                $passwd = password( I('post.password','','trim'));
                $data['password'] = $passwd['password'];
                $data['token'] = $passwd['token'];
                }
                $data['email'] = I('post.email','','trim');
                $data['roleid'] = I('post.role','','trim');
                $data['status'] = I('post.status','1','trim');
                $data['updated'] = time();

                //department
                $deparmentids = trim(I('post.deparmentids','','trim'),',');

                $re = $User->where( 'userid='.$user_id )->save($data) ; // 根据条件更新记录
                if($re)
                {
                    $deparment_arr = explode(',',$deparmentids);
                    if($deparment_arr)
                    {
                        M("Department_user")->where(array('user_id'=>$user_id))->delete();
                        foreach ($deparment_arr as $it)
                        {
                            $c_data['user_id'] = $user_id;
                            $c_data['department_id'] = $it;
                            M("Department_user")->add($c_data);
                        }
                    }

                    $this->success('编辑成功','/user/list',2);
                }else{
                    $this->error('编辑失败','/user/list',2);
                }
            }
        }else{

            $this->render('user/edit');
        }
    }
    //编辑用户
    public function profile()
    {
        $this->render('user/profile');
    }
}