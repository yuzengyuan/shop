<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
* 评论控制器
*/

class Comment extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		 $this->load->model('order_model');
		 $this->load->model('user_model');

	}

	//显示评论列表
	public function show_comment_list()
	{
		$offset= $this->uri->segment(4);

		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/comment/show_comment_list');
		$config['total_rows'] = $this->order_model->get_all_orders_good_comment_count();
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

		$data['comments'] = $this->order_model->get_all_user_comment($limit, $offset);
	//	$data['user_names'] = $this->user_model->get_user_name($data['comments']);

		$this->load->view("comment_list.html", $data);
	}

	//显示更新评论页面
	public function show_updata_comment($order_good_id)
	{
		$data['comment'] = $this->order_model->get_comment($order_good_id);
		$this->load->view("edit_comment.html",$data);
	}

	//更新评论
	public function do_updata_comment($order_good_id)
	{
		#设置验证规则
		$this->form_validation->set_rules('comment_c','评论内容','required');


		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/comment/show_updata_comment').'/'.$order_good_id;
			$this->load->view('message.html',$data);

		}
		else
		{
			$data['comment'] = $this->input->post('comment_c');
			if($this->order_model->updata_comment($order_good_id,$data))
			{
				$data['message'] = '更新评论成功';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/comment/show_comment_list');
				$this->load->view('message.html',$data);
			}
			else
			{
				$data['message'] = '更新评论失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/comment/show_updata_comment').'/'.$order_good_id;
				$this->load->view('message.html',$data);
			}
		}
	}
}