<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-7
 * Time: 下午4:04
 */

namespace Home\Controller;


use Home\Service\MessageService;
use Home\Service\UserService;

class MessageController extends BaseController
{
     function _initialize(){
            parent::_initialize();
     }

    /**
     * 信息列表
     *
     */
     function list()
     {
          if(IS_AJAX){
                $this->_ajax_list();
            }else{
                $this->render('message/list');
            }
    }
    /***
     *信息列表
     *
     */
    protected function _ajax_list()
    {
        $messlist = MessageService::getAll($this->user_info['userid']);
        foreach($messlist as $ke=>$item)
        {

            $url = U('message/detail')."/mid/".$item['messageid'];
            $messlist[$ke]['subject'] = '<a tabindex="0" class="info j-b" href="javascript:;" data-toggle="popover" data-trigger="focus" title="'.$item['subject'].'"  data-status="'.$item['status_value'].'" data-id="'.$item['messageid'].'"  data-content="'.$item['content'].'">'.$item['subject'].'</a>';
        }
        echo json_encode($messlist);
    }
    /**
     * 发信息
     */
    function add()
    {
            $userlist = UserService::listAll('','username,userid');
            $this->assign('userlist',$userlist);
            if(IS_POST){
            	$cdata['subject'] = I('post.subject','','trim');
            	$cdata['content'] = I('post.content','','strip_tags');
            	$users = I('post.users');
            	$cdata['send_from_id'] = $this->user_info['userid'];
            	$cdata['message_time']= time();
            	if($users)
            	{
            		$re = true;
            		foreach ($users as $item)
            		{
            			if($item)
            			{
            				$cdata['send_to_id'] = $item;
            				$re = ($re && (MessageService::sendMessage($cdata)));
            			}
            		}
            		if($re){
            			$this->success('消息发送成功','/message/list',1);
            		}else{
            			$this->error('消息发送失败','/message/list',1);
            		}
            	}
            	
            }else{
               $this->render('message/add');
            }
    }
    /**
     * 信息阅读
     *
     */
    public function detail()
    {
        if(!IS_AJAX) {
            $this->error('请求失败','/message/list',2);
        }
        $ids = I('post.ids','');
        if(!$ids){
            $data = array(
                'code'=> 1002,
                'msg' => '参数错误'
            );
            $this->ajaxReturn($data,'json');
        }

        $ids = trim($ids,',');
        $id_arr = explode(',',$ids);
        $res = true;
        foreach ($id_arr as $mid) {
            $res = $res &&( MessageService::readMessage($mid)); // 根据条件更新记录
        }
        if($res)
        {
            $data = array(
                'code'=> 1001,
                'msg' => '操作成功'
            );
            $this->ajaxReturn($data,'json');
        }else{
            $data = array(
                'code'=> 1003,
                'msg' => '操作失败'
            );
            $this->ajaxReturn($data,'json');
        }
    }
    /**
     * 消息删除
     *
     */
    public function dels()
    {
        if(!IS_AJAX) {
            $this->error('请求失败','/message/list',2);
        }
        $ids = I('post.ids','');
        if(!$ids){
            $data = array(
                'code'=> 1002,
                'msg' => '参数错误'
            );
            $this->ajaxReturn($data,'json');
        }

        $ids = trim($ids,',');
        $id_arr = explode(',',$ids);
        $cdata['del_type'] = 1;
        $res = true;
        foreach ($id_arr as $mid) {
            $res = $res &&( MessageService::removeMessage($mid)); // 根据条件更新记录
        }
        if($res)
        {
            $data = array(
                'code'=> 1001,
                'msg' => '操作成功'
            );
            $this->ajaxReturn($data,'json');
        }else{
            $data = array(
                'code'=> 1003,
                'msg' => '操作失败'
            );
            $this->ajaxReturn($data,'json');
        }
    }
}