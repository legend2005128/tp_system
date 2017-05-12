<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-2-13
 * Time: 下午1:37
 */
namespace Home\Controller;
use Home\Service\CustomerService;
use Home\Service\UserService;
use Think\Controller;

class CustomerController extends BaseController
{
    
	protected static $weihuren_arr;
	protected $access_user_id_arr;
    public function _initialize(){
        parent::_initialize();
        //1.操作客户权限
        $this->check_auth();
    }
    
    /**
     *
     * 用户权限判断
     *
     */
    protected function check_auth()
    {
    	$this->access_user_id_arr = UserService::getUserAndFollowCustomerIds($this->user_info['userid']);
    }
	//客户列表
    public function list()
    {
    	 $this->getWeihuren();//获取维护人列表
         $this->_ajax_list();//获取客户列表
    }
   
    /***
     * 
     * 获取维护人列表
     * 
     */
    protected function getWeihuren(){
    	if(!self::$weihuren_arr)
    	{
    		$uids = '';
    		if($this->access_user_id_arr)
    		{
    			foreach($this->access_user_id_arr as $it)
    			{
    				$uids .= $it.',';
    			}
    			$uids = "userid in (".trim($uids,',').")";
    		}
    		self::$weihuren_arr = UserService::listAll(" and username!='admin' and ".$uids."", "username,userid");
    	}
    	$this->assign('weihuren_arr',self::$weihuren_arr);
    }
    
    /***
     *
     *用户列表
     *
     */
    protected function _ajax_list()
    {
    	$page = I('get.page',0,'intval');
    	$pageSize =  25;
    	$company_name = I('get.company_name','','trim');
    	$company_area = I('get.company_area','','trim');
    	$c_time_1 = I('get.create_time_1','','trim');
    	$c_time_2 = I('get.create_time_2','','trim');
    	$company_status = I('get.is_approve','','trim');
    	$company_status = strtoupper($company_status);
    	$where = 'status=1  ';
    	if($company_name)
    	{
    		$where .= "  and company_name like '%".$company_name."%'";
    		$this->assign('company_name',$company_name);
    	}
    	if($company_area)
    	{
    		$where .= " and company_area like '%".$company_area."%'";
    		$this->assign('company_area',$company_area);
    	}
    	if($company_status )
    	{
    		if($company_status == 'APPROVE')
    		{
    			$where .= " and company_status = 'APPROVE'";
    		}else if($company_status == 'DISAPPROVE'){
    			$where .= " and company_status != 'APPROVE'";
    		}
    		$this->assign('company_status',$company_status);
    	}
    	if($c_time_1)
    	{
    		$where .= " and zn_create_time >= '".strtotime($c_time_1)."'";
    		$this->assign('create_time_1',$c_time_1);
    	}
    	if($c_time_2)
    	{
    		$where .= " and zn_create_time <= '".strtotime($c_time_2)."'";
    		$this->assign('create_time_2',$c_time_2);
    	}
    	
    	$field = '`crm_id`, `company_name`, `company_area`, `company_addr`, `link_name`, `link_mobile`, `link_phone`, `link_dept`, `link_zhiwu`, `company_url`, `company_count`, `company_faren`, `comments`, `company_status`, `from`, `create_time`, `creater`, `weihuren`,  `zn_create_time`, `status`';
        if($this->user_info['roleid'] == 1)
        {
                $User = M('Member_base');
                $count      = $User->where($where)->count();
                $Page       = new \Think\Page($count,$pageSize);
                $show       = $Page->show();
                $list = $User->where($where)->order('create_time desc ')->page($page.','.$pageSize)->select();
               // $list = $User->field($field)->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
               $this->assign('list',$list);
                $this->assign('count',$count);
                $this->assign('page',$show);
                $this->render('customer/list');
        }else{
             $user_arr =  UserService::getUserAndFollowCustomerIds($this->user_info['userid']);
             $user_ids =  implode(',',$user_arr);
             $where .= ' and crm_provider_user_id in ('.$user_ids.')';
             $User = M('Member_base');
             $count      = $User->where($where)->count();
             $Page       = new \Think\Page($count,$pageSize);
             $show       = $Page->show();
           //  $list = $User->field($field)->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
             $list = $User->where($where)->order('create_time')->page($page.','.$pageSize)->select();
             $this->assign('list',$list);
             $this->assign('page',$show);
             $this->assign('count',$count);
             $this->render('customer/list');
        }
    }
	
	/**
	 * 
	 * 变更维护人
	 */
    public function ajax_edit_weihuren(){
    	if(!IS_AJAX)
    	{
    		$this->error('请求失败','/customer/list',1);
    	}
    	$cdata['weihuren'] = I('post.whren','','strip_tags');
    	$cdata['crm_provider_user_id'] = I('post.provider_id','','trim');
    	
    	$crm_id = I('post.crm_id','','intval');
    	$where = "crm_id='".$crm_id."'";
    	if(!$cdata){
    		$data = array(
    				'code'=> 1002,
    				'msg' => '参数错误'
    		);
    		$this->ajaxReturn($data,'json');
    	}
    	$user = M('Member_base');
    	$res = $user->where( $where )->save($cdata); 
    	if($res)
    	{
    		$data = array(
    				'code'=> 1001,
    				'msg' => '操作成功'
    		);
        	}else{
    		$data = array(
    				'code'=> 1003,
    				'msg' => '操作失败'
    		);
    	}
    	$this->ajaxReturn($data,'json');
    }
	
    /***
     *
     * 删除客户列表
     *
     */
    public function ajax_del()
    {
    	if(!IS_AJAX)
    	{
    		$data = array(
    				'code'=> 1004,
    				'msg' => '请求错误'
    		);
    		$this->ajaxReturn($data,'json');
    		
    	}
    	$ck_crm_ids = I('post.ck_crm_ids','','strip_tags');
    	$ck_crm_ids = trim($ck_crm_ids,',');
    	if(!$ck_crm_ids){
    		$data = array(
    				'code'=> 1002,
    				'msg' => '参数错误'
    		);
    		$this->ajaxReturn($data,'json');
    	}
    	$crm_id_arr = explode(',',$ck_crm_ids);
    	$res = true;
    	foreach($crm_id_arr as $item)
    	{
    		$res = $res &&( M("Member_base")->where("crm_id ='".$item."'")->delete());
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
	   * 
	   * 跟进客户
	   * 
	   */
    public function genjin()
    {
    	$crm_id = I('get.crmid','','intval');
    	$customer_detail = CustomerService::getCustomerDetail('*',"crm_id='".$crm_id."'");
    	$this->check_customer_auth($customer_detail['crm_provider_user_id']);//权限判断
    	$page = I('get.page',1,'intval');
    	$pageSize =  8;
    	$where = "crm_id='".$crm_id."'";
    	$User = M('Member_genjin');
    	$count      = $User->where($where)->count();
    	$Page       = new \Think\Page($count,$pageSize);
    	$show       = $Page->show();
    	$list = $User->where($where)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign('genjin_list',$list);
    	$this->assign('count',$count);
    	$this->assign('page',$show);
    	$genjin_type_arr =array(1=>'电话',2=>'实地',3=>'其他');
    	$this->assign('genjin_type_arr',$genjin_type_arr);
      	$this->assign('customer_detail',$customer_detail);
    	if(IS_POST)
    	{
    		$rules = array(
    			array('content','require','content invaild !'),
    		);
    		$mem_genjin = M("member_genjin");
    		if (!$mem_genjin->validate($rules)->create()){
    			$error = $mem_genjin->getError();
    			$this->assign('error',$error);
    			$this->render('customer/genjin');
    			exit;
    		}else{
    			$data['content'] = I('post.content','','trim');
    			$data['genjin_type'] = I('post.genjin_type','','trim');
    			$data['create_time'] = date('Y-m-d H:i:s');
    			$data['crm_id'] =  I('post.crm_id','','intval');
    			$data['username'] = $this->user_info['username'];
    			$re = $mem_genjin->add($data);
    			if($re){
    				$this->success('新增跟进信息成功','/customer/genjin/crmid/'.$data['crm_id'],1);
    			}else{
    				$this->error('新增跟进失败','/customer/genjin/crmid/'.$data['crm_id'],1);
    			}
    	 }
    	}else{
    	$this->render();
    	}
    }
    
    /**
     *
     * 新增客户
     */
    public function add()
    {
    	$this->getWeihuren();
    	if(IS_POST)
    	{
    		$rules = array(
    				array('company_name','checkCompanyName'),
    				array('link_name','require','联系人不为空！'),
    				array('link_mobile','require','手机号不为空！'),
    				array('link_sex','require','性别不为空！'),
    				);
    		$customer_base = M("Member_base");
    		if (!$customer_base->validate($rules)->create()){
    			$error = $customer_base->getError();
    			$this->assign('error',$error);
    			$this->render();
    			exit;
    		}else{
    			$re = $this->_set_add_data('add');
    			if($re){
    				$this->success('新增成功','/customer/list',2);
    			}else{
    				$this->error('新增失败','/customer/list',2);
    			}
    		}
    	} 
    	$this->render();
    }
    
    /***
     * 
     * 设置新增的客户信息
     * 
     */
    private function _set_add_data($type='add')
    {
    	//提交数据
    	$data['company_name'] = I('post.company_name','','trim');
    	$data['company_area'] = I('post.company_area','','trim');
    	$data['company_addr'] = I('post.company_addr','','trim');
    	$data['link_name'] =  I('post.link_name','','trim');
    	$data['link_sex'] = I('post.link_sex','','trim');
    	$data['link_mobile'] = I('post.link_mobile','','trim');
    	$data['link_phone'] = I('post.link_phone','','trim');
    	$data['link_dept'] = I('post.link_dept','','trim');
    	$data['link_zhiwu'] = I('post.link_zhiwu','','trim');
    	$data['company_url'] = I('post.company_url','','trim');
    	$data['company_count'] = I('post.company_count','','trim');
    	$data['company_faren'] = I('post.company_faren','','trim');
    	$data['comments'] = I('post.comments','','trim');
    	$weihuren = I('post.weihuren','','trim');
    	$weihuren_arr = explode('-', $weihuren);
    	$data['weihuren'] = $weihuren_arr[1];
    	$data['crm_provider_user_id'] = $weihuren_arr[0];
    	//缺省数据
    	$data['company_status'] = 'APPROVE';
    	$data['from'] = 0;
    	$data['create_time'] = time();
    	$data['creater'] = $this->user_info['username'];
    	
    	$data['status'] = 1;
        if($type=='add')
        {
        	return M("Member_base")->add($data)?true:false;
        }else if($type == 'edit')
        {
        	$where['crm_id'] = I('post.crm_id','','intval');
        	echo M("Member_base")->where($where)->save($data);
        	return true;
        }
    }
    
   /***
    * 
    * crm数据详情页
    * 
    */
    public function detail()
    {
    	$crm_id = I('get.crmid','','intval');
    	$customer_detail = CustomerService::getCustomerDetail('*',"crm_id='".$crm_id."'");
    	$this->check_customer_auth($customer_detail['crm_provider_user_id']);//权限判断
    	$this->assign('cc',$customer_detail);
    	$this->render();
    }
    
    /***
     * 
     * 客户信息编辑
     */
    public function edit()
    {
    	$this->getWeihuren();
    	$crm_id = I('get.crmid','','intval');
    	$customer_detail = CustomerService::getCustomerDetail('*',"crm_id='".$crm_id."'");
    	$this->assign('cc',$customer_detail);
    	if(IS_POST)
    	{
    		$rules = array(
    				array('company_name','checkCompanyName','您输入的公司名称错误,已经存在或者名称错误！',0,'unique',1),
    				array('link_name','require','联系人不为空！'),
    				array('link_mobile','require','手机号不为空！',0,'unique',1),
    				array('link_sex','require','性别不为空！'),
    		);
    		$customer_base = M("Member_base");
    		if (!$customer_base->validate($rules)->create()){
    			$error = $customer_base->getError();
    			$this->assign('error',$error);
    			$this->render();
    			exit;
    		}else{
    			$re = $this->_set_add_data('edit');
    			if($re){
    				$this->success('客户信息成功','/customer/list',2);
    			}else{
    				$this->error('客户信息失败','/customer/list',2);
    			}
    		}
    	}else{
    		$this->render();
    	}
    }
    
    //权限判断
    protected function check_customer_auth( $crm_provider_id)
    {
    	
    	if( !in_array($crm_provider_id,$this->access_user_id_arr) && ($this->user_info['roleid']!=1))
    	{
    		$this->error('您无此权限','/',2);
    		return;
    	}
    }
    
    /***
     * 查询输入用户,查询java接口,获取信息
     * 
     */
    public function ajax_chk_znuser(){
    	
    	if(!IS_AJAX)
    	{
    		$data = array(
    				'code'=> 1004,
    				'msg' => '请求错误'
    		);
    		$this->ajaxReturn($data,'json');
    	}
    	$company_name =  I('post.company_name','','trim');
    	$data = array('authName'=>$company_name);
    	$res = curlPost(C('ZN_JAVA_API.find_detail_by_authname'),$data);
    	if(!$res['data'])
    	{
    			$this->assign('zn_info',0);
    			exit($this->display('Customer/chk_znuser'));
    	}else{
    			$this->assign('zn_info',1);
		    	$this->assign('zn_detail_other',$res['data']['infoExtend']);
		    	$this->assign('zn_detail',$res['data']['info']);
		    	exit($this->display('Customer/chk_znuser'));
    	}

    }
    /**
     * 
     * 从筑牛调取用户数据进行增加
     */
    public function add_from_zn(){
    	if(IS_POST)
    	{
    		$company_name = I('post.company_name','','trim');
    		if(!$company_name)
    		{
    			$this->error('新增失败','/customer/add',1);
    		}
    		$counts = CustomerService::getCustomerCount(array('company_name'=>$company_name));
    	
    		if ($counts){
    			
    			$this->error('用户已经在库里存在！','/customer/add',2);
    			
    		}else{
    			$re = $this->_set_add_data_from_zn();
    			if($re){
    				$this->success('新增成功','/customer/add',2);
    			}else{
    				$this->error('新增失败','/customer/add',2);
    			}
    		}
    	}
    }
    
    /***
     *
     * 设置新增的客户信息
     *
     */
    private function _set_add_data_from_zn()
    {
    	//提交数据
    	$data['company_name'] = I('post.company_name','','trim');
    	$data['company_area'] = I('post.company_area','','trim');
    	$data['company_addr'] = I('post.company_addr','','trim');
    	$data['link_name'] =  I('post.link_name','','trim');
    	$data['link_sex'] = I('post.link_sex','','trim');
    	$data['link_mobile'] = I('post.link_mobile','','trim');
    	$data['link_phone'] = I('post.link_phone','','trim');
    	$data['link_dept'] = I('post.link_dept','','trim');
    	$data['link_zhiwu'] = I('post.link_zhiwu','','trim');
    	$data['company_url'] = I('post.company_url','','trim');
    	$data['company_count'] = I('post.company_count','','trim');
    	$data['company_faren'] = I('post.company_faren','','trim');
    	$data['comments'] = I('post.comments','','trim');
    
    	$data['from'] = 1;
    	$data['zn_create_time'] = I('post.znCreateTime','','trim');
    	$data['zn_user_base_id'] = I('post.znUserBaseId','','trim');

    	//缺省数据
    	$data['company_status'] =	 I('post.company_status','','trim');
    	$data['create_time'] = time();
    	$data['creater'] = $this->user_info['username'];
    	$data['status'] = 1;
    	
    	return M("Member_base")->add($data)?true:false;
    	
    }
    
}