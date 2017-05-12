<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-1-23
 * Time: 上午9:14
 */

namespace Home\Controller;
defined('THINK_PATH') or exit();

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Think\Controller;
use Think\Verify;
class YzmController extends Controller
{
    protected   $config =array();
    public  function load()
    {
        $this->config = C('YZM');
        $verify = new Verify($this->config );
        $verify_img = $verify->entry();
        echo $verify_img;
    }
}