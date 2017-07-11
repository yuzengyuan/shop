<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Friend_link extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		
		$this->load->model('friend_link_model');
		$this->load->library('form_validation');
		
	}

	#展示友情列表
	public function show_friend_link_list(){

		$offset= $this->uri->segment(4);

		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/friend_link/show_friend_link_list');
		$config['total_rows'] = $this->friend_link_model->get_count();
		$config['per_page'] = ADMINPAGECOUNT; 
		$config['uri_segment'] = 4;

		#自定义分页信息
		$config['first_link'] = '第一页';
		$config['last_link'] = '尾页';
		$config['prev_link'] = '上一页';
		$config['next_link'] = '下一页';

		#初始化分页类
		$this->pagination->initialize($config); 
		
		#生成分页信息
		$data['pageinfo'] = $this->pagination->create_links();
		$limit = $config['per_page']; 

		$data['friends_link'] = $this->friend_link_model->get_page_friend_link($limit, $offset);

		$this->load->view('friend_link.html',$data);
	}

	#显示添加友情显示页
	public function add_friend_link(){
		$this->load->view('add_friend_link.html');
	}

	#显示编辑友情显示页
	public function edit_friend_link($friend_link_id){

		$data['friend_link'] = $this->friend_link_model->get_friend_link($friend_link_id);

		$this->load->view('edit_friend_link.html',$data);
	}

	#添加友情链接动作
	public function do_add_link(){
		#设置验证规则
		$this->form_validation->set_rules('link_name','链接名称','required');
		$this->form_validation->set_rules('link_url','链接地址','required');


		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/friend_link/add_friend_link');
			$this->load->view('message.html',$data);

		}
		else
		{
				
			#获取表单提交数据
			$data['friend_link_name'] = $this->input->post('link_name');
			$data['friend_link_url'] = $this->input->post('link_url');
			$data['email'] = $this->input->post('email');
			$data['qq'] = $this->input->post('qq');
			$data['website_content'] = $this->input->post('website_content');
			$data['is_via'] = 1;
			$data['sort_order'] = $this->input->post('sort_order');

			#调用品牌模型完成插入动作
			if($this->friend_link_model->add_friend_link($data))
			{
				$data['message'] = '添加友情链接成功';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/friend_link/show_friend_link_list');
				$this->load->view('message.html',$data);
			}
			else
			{
				$data['message'] = '添加友情链接失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/friend_link/add_friend_link');
				$this->load->view('message.html',$data);

			}
			
		}
	}

	#更新友情链接动作
	public function do_updata_link()
	{
		
		$friend_link_id = $this->input->post('link_h_id');
		

		#设置验证规则
		$this->form_validation->set_rules('link_name','链接名称','required');
		$this->form_validation->set_rules('link_url','链接地址','required');

		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/friend_link/edit_friend_link').'/'.$friend_link_id;
			$this->load->view('message.html',$data);
		}
		else
		{	
			#获取表单提交数据
			$data1['friend_link_name'] = $this->input->post('link_name');
			$data1['friend_link_url'] = $this->input->post('link_url');
			$data1['email'] = $this->input->post('email');
			$data1['qq'] = $this->input->post('qq');
			$data1['website_content'] = $this->input->post('website_content');
			$data1['is_via'] = $this->input->post('is_via');
			if(!empty($data1['is_via']))
			{
				$data1['is_via'] = 1;
			}
			else
			{
				$data1['is_via'] = 0;
			}
			$data1['sort_order'] = $this->input->post('sort_order');

			if($this->friend_link_model->updata_friend_link($friend_link_id, $data1))
			{
				$data['message'] = '更新友情链接成功';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/friend_link/show_friend_link_list');
				$this->load->view('message.html',$data);
			}
			else
			{
				$data['message'] = '更新友情链接失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/friend_link/edit_friend_link').'/'.$friend_link_id;
				$this->load->view('message.html',$data);

			}
		
		}
	}

	#删除友情链接
	public function do_del_link($friend_link_id)
	{
		if($this->friend_link_model->del_friend_link($friend_link_id))
		{
			$data1['message'] = '删除友情链接成功';
			$data1['wait'] = 3;
			$data1['url'] = site_url('admin/friend_link/show_friend_link_list');
			$this->load->view('message.html',$data1);
		}
		else
		{
			$data1['message'] = '删除友情链接失败';
			$data1['wait'] = 3;
			$data1['url'] = site_url('admin/friend_link/edit_friend_link').'/'.$friend_link_id;
			$this->load->view('message.html',$data1);

		}
	}
		

	

}