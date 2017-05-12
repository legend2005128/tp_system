<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-25
 * Time: 上午9:12
 */
return array(
    // 开启路由
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES'=>array(
    'login'          => 'auth/login',
    'passport'       =>'auth/check_login',
    'loginout'       => 'auth/loginout',
        
        'profile'    => 'user/profile',

    )
);