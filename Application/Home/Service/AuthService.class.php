<?php

/**
 * 用户认证服务
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-21
 * Time: 下午2:12
 */
namespace Home\Service;

use Think\Model;
class AuthService extends \Home\Interfaces\AuthInterface
{

    /**
     * 获取用户信息
     */
    public  static  function getUserAuthInfo(  )
    {
        $user_token = cookie('crm_uuid');
        //查数据库
        return M("Users")->join('__ROLE__ ON __USERS__.	roleid = __ROLE__.roleid')->where("token='".$user_token."'")->limit(1)->find();
    }

    /**
     * 校验用户是否登录
     */
    public  static  function checkAuthLogin( $data = array(),$token_ses_key = false)
    {

        if( $data )
        {
            if( isset($data['username']) )
            {
            	$remember = $data['remember'];
                $User1 = M("Users"); // 实例化User对象
                $condition['username'] =  $data['username'];
                $condition['status'] = 1;
                // 把查询条件传入查询方法
                $user_info = $User1->where($condition)->limit(1)->find();
                if(!$user_info)
                {
                    return array('result'=>0,'msg'=>L('username_f1'));
                }else{

                    if( $user_info['password'] !== password( $data['password'],$user_info['token']))
                    {
                        return array('result'=>0,'msg'=>L('passwd_f1'));
                    }

                    $data = array(
                                'lastloginip'=>ip(),
                                'lastlogintime'=>time()
                            );
                    $q = M("Users")-> where('userid='.$user_info['userid'])->setField($data);

                    if($q)
                    {
                        if($token_ses_key)
                        {
                            session($token_ses_key,null);
                        }
                        //设置session or cookie
                        self::settUserAuthInfo($user_info,$remember);
                        return array('result'=>1,'msg'=>L('login_suc'));
                    }
                }

            }

        }else{
            return array('result'=>0,'msg'=>L('login_f2'));
        }

    }

    /**
     * 设置用户信息
     */
    public  static  function settUserAuthInfo( array $data ,$remember =false)
    {
    	if($remember)
    	{
    		$exprie_time = 3600*24*30;
    	}
    	else{
    		$exprie_time = 3600*2;
    	}
    
        cookie('crm_uuid',$data['token'],$exprie_time);//one month logining 
    }

    /**
     * 用户退出登录
     */
    public  static  function setAuthLoginOut()
    {
        $uco = cookie();
        foreach ($uco as $key=>$v)
        {
            cookie($key,null);
        }
        $use = session();
        foreach ($use as $key=>$v)
        {
            session($key,null);
        }
    }

}