<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//公告控制器
class Notice extends Admin_Controller{

	public function __construct(){
		parent::__construct();

		$this->load->library('form_validation');
		$this->load->model('notice_model');
		
	}

	//显示公告列表
	public function show_notic_list()
	{
		$data['notices'] = $this->notice_model->get_notice();
		$this->load->view("notic_list.html",$data);
	}

	//显示添加公告
	public function show_add_notice()
	{
		
		$this->load->view("notice_add.html");
	}

	//添加公告
	public function do_add_notice()
	{
		#设置验证规则
		$this->form_validation->set_rules('notice_title',"通告标题",'required');
		$this->form_validation->set_rules('notice_desc','通告内容','required');


		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/notice/show_add_notice');
			$this->load->view('message.html',$data);

		}
		else
		{
			$data['notice_title'] = $this->input->post('notice_title');
			$data['notice_content'] = $this->input->post('notice_desc');
			$data['add_time'] = time();

			if ($this->notice_model->add_notice($data)){
				#插入ok
				$data['message'] = '添加公告成功';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/notice/show_notic_list');
				$this->load->view('message.html', $data);
			}else{
				#插入失败
				$data['message'] = '添加公告失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/notice/show_add_notice');
				$this->load->view('message.html', $data);
			}
		}
	}

	//显示编辑公告
	public function show_edit_notice($notice_id)
	{
		$data['notice'] = $this->notice_model->get_row_notice($notice_id);
		$this->load->view("notice_edit.html",$data);
	}

	//编辑公告
	public function do_edit_notice($notice_id)
	{
		#设置验证规则
		$this->form_validation->set_rules('notice_title',"通告标题",'required');
		$this->form_validation->set_rules('notice_desc','通告内容','required');


		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/notice/show_edit_notice').'/'.$notice_id;
			$this->load->view('message.html',$data);

		}
		else
		{
			$data['notice_title'] = $this->input->post('notice_title');
			$data['notice_content'] = $this->input->post('notice_desc');
			$data['add_time'] = time();

			if ($this->notice_model->updata_notice($notice_id,$data)){
				#插入ok
				$data['message'] = '更新公告成功';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/notice/show_notic_list');
				$this->load->view('message.html', $data);
			}else{
				#插入失败
				$data['message'] = '更新公告失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/notice/show_edit_notice').'/'.$notice_id;
				$this->load->view('message.html', $data);
			}
		}
	}

	//删除公告
	public function del_notice($notice_id)
	{
		if ($this->notice_model->del_notice($notice_id)){
			#插入ok
			$data['message'] = '删除公告成功';
			$data['wait'] = 1;
			$data['url'] = site_url('admin/notice/show_notic_list');
			$this->load->view('message.html', $data);
		}else{
			#插入失败
			$data['message'] = '删除公告失败';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/notice/show_notic_list');
			$this->load->view('message.html', $data);
		}
	}
}