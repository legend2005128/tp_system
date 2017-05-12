<?php
/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
	$p = new Think\Page($count, $pagesize);
	$p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
	$p->setConfig('prev', '上一页');
	$p->setConfig('next', '下一页');
	$p->setConfig('last', '末页');
	$p->setConfig('first', '首页');
	$p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
	$p->lastSuffix = false;//最后一页不显示为总页数
	return $p;
}


/**
 * 验证码
 *检测输入的验证码是否正确，$code为用户输入的验证码字符串
 */

function check_verify($code, $id = ''){
    $verify = new \Think\Verify(C('YZM'));
    return $verify->check($code, $id);
}
/*
 * 页面跳转会话
 */
function Msgs($msg, $url = '', $time = 3, $type = 1)
{
    if (empty($msg)) {
        $msg = '操作成功';
    }
    if (empty($url)) {
        $url = 'javascript:history.back(-1)';
    }

    $code = '<!DOCTYPE>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=7" />
        <title>提示信息</title>
        <style type="text/css">
        <!--
        *{ padding:0; margin:0; font-size:12px}
        a:link,a:visited{text-decoration:none;color:#0068a6}
        a:hover,a:active{color:#ff6600;text-decoration: underline}
        .showMsg{border: 1px solid #1e64c8; zoom:1; width:450px; height:172px;position:absolute;top:44%;left:50%;margin:-87px 0 0 -225px}
        .showMsg h5{background-image: url("/Public/common/images/msg.png");background-repeat: no-repeat; color:#fff; padding-left:35px; height:25px; line-height:26px;*line-height:28px; overflow:hidden; font-size:14px; text-align:left}
        .showMsg .content{ padding:46px 12px 10px 45px; font-size:14px; height:64px; text-align:left}
        .showMsg .bottom{ background:#e4ecf7; margin: 0 1px 1px 1px;line-height:26px; *line-height:30px; height:26px; text-align:center}
        .showMsg .ok,.showMsg .guery{background: url("/Public/common/images/msg_bg.png") no-repeat 0px -560px;}
        .showMsg .guery{background-position: left -460px;}
        -->
        </style>
        </head>
        <body>
        <div class="showMsg" style="text-align:center">
            <h5>提示信息</h5>
            <div class="content guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline;max-width:330px">' . $msg . '<b id="wait">' . $time . '</b> 秒后 跳转</div>
            <div class="bottom"><a href="' . $url . '" id="href">如果您的浏览器没有自动跳转，请点击这里</a></div>
        </div>
        <script type="text/javascript">
        var wait = document.getElementById(\'wait\'),href = document.getElementById(\'href\').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time == 0) {
                if(href != "" && href != null && href != "undefined"){
                    window.location.href = href;
                }else{
                    window.location.href = window.history.go(-1);
                }
                clearInterval(interval);
            };
        }, 1000);
        </script>
        </body>
        </html>';
    exit($code);
}

/**
 * ajax_response
 *response_ajax('-1','验证码错误','auth/login');
 */
function response_text( string $code, string $msg,string $url='',string $type='json')
{
     $data['code'] = $code;
     $data['msg'] = $msg;
    $data['url'] = $url;
     if($type == 'json'){
         exit(json_encode($data));
     }else if($type=='array'){
         return $data;
     }

}


/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip() {
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
    $pwd = array();
    $pwd['token'] =  $encrypt ? $encrypt : create_randomstr();
    $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}
/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
    return do_hash(time().rand());
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * String hash encryption
 *
 * @access public
 * @param string $string
 * @param string $salt
 * @return string
 */
function do_hash($string, $salt = NULL) {
    if (null === $salt) {
        $salt = sha1('XHDAF');
    } else {
        $salt = sha1($salt);
    }

    return md5($salt . $string);
}

/**
 * 字符串加密、解密函数
 *
 *
 * @param	string	$txt		字符串
 * @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
 * @param	string	$key		密钥：数字、字母、下划线
 * @param	string	$expiry		过期时间
 * @return	string
 */
function set_cookie_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key != '' ? $key : 'zhuniu168');
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(strtr(substr($string, $ckey_length), '-_', '+/')) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
    }
}
//检测用户密码
function checkPwd( $password ){
    preg_match('/^[a-zA-Z0-9_]{4,16}$/',$password,$arr);
    if($arr[0])
    {
        return true;
    }
    return false;
}
//检测用户名称
function checkUname($uname){
    preg_match('/^\\w+$/',$uname,$arr);
    if($arr[0])
    {
        return true;
    }
    return false;

}
//检测客户名称
function checkCompanyName($company_name){
	preg_match('/[\x{4e00}-\x{9fa5}]+/u',$company_name,$arr);

	if($arr[0])
	{
		return true;
	}
	return false;

}
//格式化时间输出
function time_tran($the_time){
    $now_time = date("Y-m-d H:i:s",time()+8*60*60);
    $now_time = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if($dur < 0){
        return $the_time;
    }else{
        if($dur < 60){
            return $dur.'秒前';
        }else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return $the_time;
                    }
                }
            }
        }}}
function format_url($url)
{
	if(!$url)
	{
		return U('/');
	}
   else if( strpos($url,'-')!==false)
    {
        return U(str_replace('-','/',$url));
    }
}
/**
 * 获取菜单
 */
function getMenuList()
{
    $menuList = array();
    $menuList = \Home\Service\MenuService::getAllTopMenu();
    foreach ($menuList as $key => $item) {
        $menuList[$key]['children'] = \Home\Service\MenuService::getMenu($item['menuid']);
    }
    return $menuList;
}

/**
 * 获取所有部门
 *
 */
  function getAllDepartment( $dept_arr  )
{
        static $delist ;
        foreach ($dept_arr as $key => $value) 
             {
                    if($value['isparent'])
                    {
                        $dept_arr = \Home\Service\DepartmentService::getDepartment($value['id']);
                        $delist[]= $dept_arr;
                        if( $dept_arr )
                        {
                            getAllDepartment( $dept_arr );
                        }
                    }
                    continue;
        }
        return $delist;
}

/**
 * @param $url
 * @param array $data
 * @param string $type 返回类型 array json
 * @return mixed
 * curl  post方式Http请求封装
 */
function curlPost($url, $data = array(), $type = 'array', $json = true)
{
	G('begin');
	$ch = curl_init();
	if ($json) { //发送JSON数据
		$headers = array('Content-Type: application/json; charset=utf-8');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if (is_array($data)) {
			$data = json_encode($data);
		}
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	//记录非正常接口日志信息
	if ($httpCode != 200) {
		\Think\Log::write('接口状态：' . $httpCode . ' URL地址：' . $url . ', 参数:' . json_encode($data));
	}
	G('end');
	if (APP_DEBUG) {
		$waste =  G('begin','end').'s';
		trace( $waste."  ".$url." => 状态:".$httpCode ,'列表:','debug_api');
		trace( $url." =>  ".$result ,'数据:','debug_data');
	}
	if ($type == 'json') {
		return $result;
	} else {
		return json_decode($result, true);
	}
}