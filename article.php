<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//文章控制器
class Article extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		  $this->load->model('article_model');
	}

	//显示文章分类列表
	public function show_article_cat()
	{
		$data['cats'] = $this->article_model->get_cat();


		$this->load->view("article_cat.html",$data);
	}

	//显示文章列表
	public function show_article_list()
	{
		$data['articles'] = $this->article_model->get_article();
	//	print_r($data['articles']);die;
		$this->load->view("article_list.html", $data);
	}

	//显示添加文章
	public function show_add_article()
	{
		$data['cats'] = $this->article_model->get_cat();
		$this->load->view("add_article.html",$data);
	}

	//显示编辑文章
	public function show_edit_article($article_id)
	{
		$data['cats'] = $this->article_model->get_cat();
		$data['article'] = $this->article_model->get_article_id($article_id);

		$this->load->view("edit_article.html",$data);
	}

	//显示编辑文章分类
	public function show_edit_cat($cat_id)
	{
		$data['cat'] = $this->article_model->get_assign_cat($cat_id);
		$this->load->view("edit_article_cat.html",$data);
	}

	//显示添加文章分类
	public function show_add_article_cat()
	{
		
		$this->load->view("add_article_cat.html");
	}

	//添加文章分类
	public function add_article_cat()
	{
			
		#设置验证规则
		$this->form_validation->set_rules('cat_name','分类名称','trim|required');
		if($this->form_validation->run() == false){
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/article/show_add_article_cat');
			$this->load->view('message.html', $data);
		}else{
		
			#通过验证
			$data['cat_name'] = $this->input->post('cat_name',true);
			$data['sort_order'] = $this->input->post('sort_order',true);
			$data['is_show'] = $this->input->post('is_show');
			$data['cat_desc'] = $this->input->post('cat_desc',true);

			#调用model完成插入
			
			if ($this->article_model->add_category($data)){
				#插入ok
				$data['message'] = '添加文章分类成功';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/article/show_article_cat');
				$this->load->view('message.html', $data);
			}else{
				#插入失败
				$data['message'] = '添加文章失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/article/show_add_article_cat');
				$this->load->view('message.html', $data);
			}
		}
	}

	//编辑文章分类
	public function edit_article_cat()
	{
		$cat_id = $this->input->post('cat_id');

		#设置验证规则
		$this->form_validation->set_rules('cat_name','分类名称','trim|required');
		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/article/show_add_article_cat');
			$this->load->view('message.html', $data);
		}
		else
		{
			#通过验证
			$data['cat_name'] = $this->input->post('cat_name',true);
			$data['sort_order'] = $this->input->post('sort_order',true);
			$data['is_show'] = $this->input->post('is_show');
			$data['cat_desc'] = $this->input->post('cat_desc',true);

			#调用model完成插入
			if ($this->article_model->updata_category($cat_id,$data))
			{
				#插入ok
				$data['message'] = '更新文章分类成功';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/article/show_article_cat');
				$this->load->view('message.html', $data);
			}
			else
			{
				#插入失败
				$data['message'] = '更新文章分类失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/article/show_edit_cat').'/'.$cat_id;
				$this->load->view('message.html', $data);
			}
		}
		
	}

	//编辑文章
	public function edit_article()
	{
		$article_id = $this->input->post('article_id');

		#设置验证规则
		$this->form_validation->set_rules('title','文章名称','trim|required');
		$this->form_validation->set_rules('goods_desc','文章内容','trim|required');
		if($this->form_validation->run() == false){
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/article/show_edit_article').'/'.$article_id;
			$this->load->view('message.html', $data);
		}else{
		
			#通过验证
			$data['article_title'] = $this->input->post('title',true);
			$data['article_content'] = $this->input->post('goods_desc');
			$data['cat_id'] = $this->input->post('article_cat');

	
			if ($this->article_model->updata_article($article_id,$data)){
				#插入ok
				$data['message'] = '更新文章成功';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/article/show_article_list');
				$this->load->view('message.html', $data);
			}else{
				#插入失败
				$data['message'] = '更新文章失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/article/show_edit_article').'/'.$article_id;
				$this->load->view('message.html', $data);
			}
		}

	}


	//添加文章
	public function add_article()
	{
		#设置验证规则
		$this->form_validation->set_rules('title','文章名称','trim|required');
		$this->form_validation->set_rules('goods_desc','文章内容','trim|required');
		if($this->form_validation->run() == false){
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/article/show_add_article');
			$this->load->view('message.html', $data);
		}else{
		
			#通过验证
			$data['article_title'] = $this->input->post('title',true);
			$data['article_content'] = $this->input->post('goods_desc');
			$data['cat_id'] = $this->input->post('article_cat');

	
			if ($this->article_model->add_article($data)){
				#插入ok
				$data['message'] = '添加文章成功';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/article/show_article_list');
				$this->load->view('message.html', $data);
			}else{
				#插入失败
				$data['message'] = '添加文章失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/article/show_add_article');
				$this->load->view('message.html', $data);
			}
		}
	}

	//删除分类
	public function del_cat($cat_id)
	{
		$articles = $this->article_model->get_assign_cat_artrice($cat_id);

		if(!empty($articles))
		{
			$data['message'] = '请确保文章类别下面没有文章!';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/article/show_article_cat');
			$this->load->view('message.html', $data);
		}
		else
		{
	
			if($this->article_model->del_cat($cat_id))
			{
				$data['message'] = '删除文章分类成功!';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/article/show_article_cat');
				$this->load->view('message.html', $data);
			}
			else
			{
				$data['message'] = '删除文章分类失败!';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/article/show_article_cat');
				$this->load->view('message.html', $data);
			}
			
		}
	}


	//删除文章
	public function del_article($article_id)
	{
		if($this->article_model->del_article($article_id))
		{
			$data['message'] = '删除文章成功!';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/article/show_article_list');
			$this->load->view('message.html', $data);
		}
		else
		{
			$data['message'] = '删除文章失败!';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/article/show_article_list');
			$this->load->view('message.html', $data);
		}
	}

}