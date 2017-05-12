<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-23
 * Time: 下午1:39
 */


return array(
    'YZM' => ARRAY(
    'fontSize'    =>    30,    // 验证码字体大小
    'length'      =>    4,     // 验证码位数
    'useNoise'    =>    TRUE, // 关闭验证码杂点
    'imageW'      =>  200,
    'imageH'      =>  60,
    'expire'      => 180,       //验证码失效时间
    'seKey'     =>  'zn168',   // 验证码加密密钥
    'useImgBg' =>true
    )

);