<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//用户控制器
class User extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('order_model');
		
		
	}

	//显示用户列表
	public function show_user_list()
	{
		$data['users'] = $this->user_model->get_all_user();
		$this->load->view('user_list.html',$data);
	}

	//显示添加页面
	public function show_add_user()
	{
		$this->load->view('add_user.html');
	}

	//添加用户
	public function add_user()
	{
		#设置验证规则
		$this->form_validation->set_rules('email',"邮箱",'required|valid_email');
		$this->form_validation->set_rules('pwd',"用户密码",'required');
		$this->form_validation->set_rules('phone',"用户手机",'required');
		
		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/user/show_add_user');
			$this->load->view('message.html',$data);
		}
		else
		{
			$data['email'] = trim($this->input->post('email'));
			$data['user_password'] = md5($this->input->post('pwd'));
			$data['phone'] = trim($this->input->post('phone'));
			$data['add_time'] = time();

			$status = $this->user_model->check_user($data['email']);

			if(!empty($status))
			{
				$data['message'] = '用户名已被使用';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/user/show_add_user');
				$this->load->view('message.html', $data);
			}
			else
			{
				if($this->user_model->add_user($data))
				{
					#ok
					$data['message'] = '添加用户成功';
					$data['wait'] = 1;
					$data['url'] = site_url('admin/user/show_user_list');
					$this->load->view('message.html', $data);
				}
				else
				{
					$data['message'] = '添加用户失败';
					$data['wait'] = 1;
					$data['url'] = site_url('admin/user/show_add_user');
					$this->load->view('message.html', $data);
				}
			}
			
		}
	}

	//显示用户留言列表
	public function show_user_msg_list()
	{
		$data['msgs'] = $this->user_model->get_all_complain();
		$this->load->view('user_msg_list.html',$data);
	}

	//显示用户留言
	public function show_user_msg($complain_id)
	{
		$data['complains'] = $this->user_model->get_user_complain($complain_id);
		$this->load->view('user_msg.html',$data);
	}

	//回复用户留言
	public function reply_user_msg($complain_id)
	{
		
	}

	//显示编辑页面
	public function show_edit_user($user_id)
	{
		$data['user'] = $this->user_model->get_id_user($user_id);
		$this->load->view('edit_user.html',$data);
	}

	//编辑用户
	public function edit_user()
	{
		$user_id = $this->input->post('user_id');

		#设置验证规则
		$this->form_validation->set_rules('pwd',"用户密码",'required');
		$this->form_validation->set_rules('phone',"用户手机",'required');
		
		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/user/show_edit_user').'/'.$user_id;
			$this->load->view('message.html',$data);
		}
		else
		{
			$data['user_password'] = md5($this->input->post('pwd'));
			$data['phone'] = trim($this->input->post('phone'));
			$data['add_time'] = time();

			if($this->user_model->update_user($user_id, $data))
			{
				#ok
				$data['message'] = '更新用户成功';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/user/show_user_list');
				$this->load->view('message.html', $data);
			}
			else
			{
				$data['message'] = '更新用户失败';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/user/show_edit_user').'/'.$user_id;
				$this->load->view('message.html', $data);
			}
		}
	}

	//显示用户所有订单
	public function user_all_order($user_id)
	{
		$data['orders'] = $this->user_model->get_user_all_order($user_id);


		$this->load->view('order_list.html',$data);
	}

	//显示用户地址列表
	public function show_user_address($user_id)
	{
		$data['addrs'] = $this->user_model->check_all_adds($user_id);
		$this->load->view('address_list.html',$data);
	}

}