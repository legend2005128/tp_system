<?php
/**
 * 用户消息服务
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-7
 * Time: 上午11:29
 */
namespace Home\Service;

class MessageService
{
        //获取信息数目
        public static function getNum($uid)
        {
            return M("Message")->where("send_to_id='".$uid."' and 	del_type =0")->count();
        }
        //获取信息
        public static function getAll($uid)
        {
            return M('Message')->field(''.C('db_prefix').'message.status as status_value,FROM_UNIXTIME(message_time) AS message_time,if('.C('db_prefix').'message.status=1,"已读","未读") as status,username as sender,subject,content,messageid')->join('LEFT JOIN __USERS__ ON __USERS__.userid=__MESSAGE__.send_from_id')->where("send_to_id='".$uid."' and 	del_type =0 ")->order('status_value asc')->select();

        }
        //获取未读信息
        public static function getNoread($uid)
        {
            return M('Message')->field('username as sender,subject,content,messageid,message_time')->join('LEFT JOIN __USERS__ ON __USERS__.userid=__MESSAGE__.send_from_id')->where("send_to_id='".$uid."' and  del_type =0  and ".C('db_prefix')."message.status = 0 ")->order('message_time desc')->select();
        }
        //发送信息
        public static function sendMessage($data)
        {
            return M("Message")->add($data);
        }
        //read message
        public static function readMessage($mid)
        {
            $data['status']= 1;
            return M("Message")->where("messageid='".$mid."'")->save($data);
        }
        //删除消息
        public static function removeMessage($mid)
        {
            $data['del_type']= 1;
             M("Message")->where("messageid='".$mid."'")->save($data);
             return true;
        }
        //删除个人消息
        public static function removeMessageByuid($uid)
        {
            return M("Message")->where("send_to_id='".$uid."'")->delete();
        }
}