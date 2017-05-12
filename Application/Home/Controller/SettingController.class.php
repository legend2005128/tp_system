<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-7
 * Time: 下午3:05
 */

namespace Home\Controller;


class SettingController extends BaseController
{
    public function _initialize(){
        parent::_initialize();
    }

    public function index(){
        if(IS_POST)
        {
            $rules = array(
                array('password','checkPwd',L('password_err'),0,'function'),
                array('repassword','password',L('repassword_err'),0,'confirm'),
                array('email','email','email invaild type!'),
            );
            $User = M("Users");
            if (!$User->validate($rules)->create()){
                $error = $User->getError();
                $this->assign('error',$error);
                $this->render('setting/index');
                exit;
            }else{
                $user_id = $this->user_info['userid'];
                $passwd = password( I('post.password','','trim'));
                $data['password'] = $passwd['password'];
                $data['token'] = $passwd['token'];
                $data['email'] = I('post.email','','trim');
                $data['updated'] = time();
                $re = $User->where( 'userid='.$user_id )->save($data) ; // 根据条件更新记录
                if($re)
                {
                    $this->success('编辑成功','/',2);
                }else{
                    $this->error('编辑失败','/',2);
                }
            }
        }else{
            $this->render('setting/index');
        }

    }
}