<?php
$arr = array(
    'MODULE_ALLOW_LIST'          => array('Home'),
    'DEFAULT_MODULE'             => 'Home',
    'URL_CASE_INSENSITIVE'  =>  true,//url不区分大小写
    'URL_MODEL' => 2, //url兼容模式
    'URL_HTML_SUFFIX' => '', //去掉url的后缀
    'DEFAULT_FILTER'  => 'strip_tags',
    'LOAD_EXT_CONFIG' => 'database,yzm,route',//加载配置
	'ERROR_PAGE' =>'/Public/error/404.html',//404页面
	'LOG_RECORD' => true, // 开启日志记录
	'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR',
		
	//'SHOW_PAGE_TRACE' =>true,//性能测试工具
	//'DB_SQL_BUILD_CACHE' => true,

		
);
if(APP_DEBUG){
	$arr['ZN_JAVA_API'] = array(
	'find_detail_by_authname'=>	'http://192.168.2.32:8080/user/findDetailByAuthName'
	);
}else{
	//线上
	$arr['ZN_JAVA_API'] = array(
	'find_detail_by_authname'=>	'http://192.168.2.32:8080/user/findDetailByAuthName'
	);
}
return $arr;