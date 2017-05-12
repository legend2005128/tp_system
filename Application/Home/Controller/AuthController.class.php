<?php
/**
 * Auth Controller
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-21
 * Time: 下午2:35
 */

namespace Home\Controller;

use Behavior\TokenBuildBehavior;
use Home\Service\AuthService;
use Think\Controller;

class AuthController extends  Controller
{
        protected $_msg;//提示信息
		

        //login check
        public function check_login()
        {
            $post_data['username'] = I('post.uname','','trim');
            $post_data['password'] = I('post.upassword','','trim');
            $name   = C('TOKEN_NAME', null, '__hash__');
            $post_data[$name] = I('post.token','','trim');
            $post_data['code'] = I('post.yzm','','trim');
            $post_data['remember'] = I('post.remember','','trim');
          
            $ref = I('post.ref')?I('post.ref'):'/';
            //验证码检测
            $yzm_ck = check_verify( $post_data['code'] );
            if( !$yzm_ck || empty($post_data['code']) )
            {
                $this->_msg = L('yzm_err');
                response_text('1002', $this->_msg);
            }
            //token检测
            $token_ck = $this->checkToken($post_data);
            if( !$token_ck || empty($post_data[$name]))
            {
                response_text('1003',$this->_msg);
            }
            $authention_ck = false;
            $authention_ck = AuthService::checkAuthLogin($post_data,$token_ck);
            if($authention_ck)
            {
                if(!$authention_ck['result'])
                {
                    response_text('1004', $authention_ck['msg']);
                }else{
                    response_text('1001', $authention_ck['msg'],U($ref));
                }

            }
        }

        //login page
        public function login()
        {
        	$ref = I('get.ref','/','trim');
            if( AuthService::getUserAuthInfo() )
            {
                redirect(U($ref));
            }
            $this->assign('ref',$ref);
            $this->display();
        }

        //login page
        public function loginout()
        {
            AuthService::setAuthLoginOut();
            $this->success('退出成功','/login',2);
        }

        //token check
        protected function checkToken($data) {
            // 支持使用token(false) 关闭令牌验证
            if(isset($data['token']) && !$data['token']) return true;
            if(C('TOKEN_ON')){
                $name   = C('TOKEN_NAME', null, '__hash__');
                if(!isset($data[$name]) || !(session($name))) { // 令牌数据无效
                    $this->_msg .=  L('token_wx');
                    return false;
                }
                // 令牌验证
                list($key,$value)  =  explode('_',$data[$name]);
                if( session($name.'.'.$key)  && $value &&(session($name.'.'.$key) === $value)){// 防止重复提交
                    $this->_msg = L('token_suc');
                    return $name.'.'.$key;

                }
                // 开启TOKEN重置
                if(C('TOKEN_RESET')) session($name.'.'.$key,null);
                $this->_msg .= L('token_fail');
                return false;
            }
            return true;
        }

}