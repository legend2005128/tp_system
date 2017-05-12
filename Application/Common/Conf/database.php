<?php
/**
 * database config
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-21
 * Time: 上午10:14
 */
if(APP_DEBUG){
    return
    array(
    		'db_type'  => 'mysqli',
            		'db_user'  => '222',
            		'db_pwd'   => 'ch@',
            		'db_host'  => '127.0.0.1',
            		'db_port'  => '3306',
            		'db_name'  => 'zn_crm',
            		'db_prefix' =>'crm_'
    
    );
}
//线上环境
    return
            array(
            		'db_type'  => 'mysqli',
            		'db_user'  => 'root',
            		'db_pwd'   => 'ch@',
            			'db_host'  => '127.0.0.1',
            		'db_port'  => '3306',
            		'db_name'  => 'zn_crm',
            		'db_prefix' =>'crm_'

            );