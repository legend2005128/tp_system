<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-21
 * Time: 下午2:04
 */

namespace Home\Interfaces;


class AuthInterface
{
    /**
     * 获取用户信息
     */
     public  static  function getUserAuthInfo(){}

    /**
     * 校验用户是否登录
     */
    public  static  function checkAuthLogin(){}

    /**
     * 用户退出登录
     */
    public  static  function setAuthLoginOut(){}

    /**
     * 设置用户信息
     */
    public  static  function settUserAuthInfo(array $data ,$remember =false){}


}