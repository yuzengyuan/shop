<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品品牌控制器

class Brand extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('brand_model');
		$this->load->model('goods_model');
		$config['upload_path'] = './public/brand_img/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = 100;

		$this->load->library('upload', $config);
	}
	#显品牌信息
	public function index(){

		$offset= $this->uri->segment(4);

		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/brand/index');
		$config['total_rows'] = $this->brand_model->count_brand();
		$config['per_page'] = 10; 
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

		#获取品牌信息
		$data['brands'] = $this->brand_model->list_get_brand($limit, $offset);
		$this->load->view('brand_list.html', $data);
	}

	#显示添加品牌页面
	public function add(){
		$this->load->view('brand_add.html');
	}

	#显示编辑品牌页面
	public function edit(){
		$brand_id = $this->uri->segment(4);
		$data['brand'] = $this->brand_model->get_brand($brand_id);
		$this->load->view('brand_edit.html', $data);
	}

	#添加品牌
	public function insert(){
		#设置验证规则
		$this->form_validation->set_rules('brand_name','品牌名称','required');

		if($this->form_validation->run() == false){
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/brand/add');
			$this->load->view('message.html',$data);

		}else{

			# 通过验证,处理图片上传
			#配置上传文件相关参数

			if ($this->upload->do_upload('logo')){
				#上传成功,获取文件名
				$fileinfo = $this->upload->data();
				$data['logo'] = $fileinfo['file_name'];
				#获取表单提交数据
				$data['brand_name'] = $this->input->post('brand_name');
				$data['url'] = $this->input->post('url');
				$data['brand_desc'] = $this->input->post('brand_desc');
				$data['sort_order'] = $this->input->post('sort_order');
				$data['is_show'] = $this->input->post('is_show');

				#调用品牌模型完成插入动作
				if($this->brand_model->add_brand($data)){
					$data['message'] = '添加品牌成功';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/brand/index');
					$this->load->view('message.html',$data);
				}else{
					$data['message'] = '添加品牌失败';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/brand/add');
					$this->load->view('message.html',$data);

				}
			}else{
				#上传失败
				$data['message'] = $this->upload->display_errors();
				$data['wait'] = 3;
				$data['url'] = site_url('admin/brand/add');
				$this->load->view('message.html',$data);

			}
		}

	}

	#更新商品品牌
	public function updata(){
		$brand_id = $this->input->post('brand_id');
		$data['brand_name'] = $this->input->post('brand_name');
		$data['url'] = $this->input->post('url');
		$data['logo'] = $_FILES['logo']['name'];
		$data['brand_desc'] = $this->input->post('brand_desc');
		$data['sort_order'] = $this->input->post('sort_order');
		$data['is_show'] = $this->input->post('is_show');
		
		#设置验证规则
		$this->form_validation->set_rules('brand_name','品牌名称','required');

		if($this->form_validation->run() == false){
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/brand/add');
			$this->load->view('message.html',$data);
		}else{
			# 通过验证,处理图片上传
			#配置上传文件相关参数
			if($data['logo'] != "")
			{
				$logo_name = $this->input->post('oldLogo');
				$logo_add = dirname(dirname(dirname(dirname(__FILE__)))).'\\public\brand_img'.'\\'.$logo_name;

			
				if (file_exists($logo_add))
			    {
			       if(!unlink($logo_add)){
			       		$data['message'] = '更新品牌失败';
						$data['wait'] = 3;
						$data['url'] = site_url('admin/brand/add');
						$this->load->view('message.html',$data);
			       }
			    }

				if ($this->upload->do_upload('logo')){
					#上传成功,获取文件名
					$fileinfo = $this->upload->data();
					$data['logo'] = $fileinfo['file_name'];

					#调用品牌模型完成插入动作
					if($this->brand_model->updata_brand($brand_id,$data)){
						$data['message'] = '更新品牌成功';
						$data['wait'] = 3;
						$data['url'] = site_url('admin/brand/index');
						$this->load->view('message.html',$data);
					}else{
						$data['message'] = '更新品牌失败';
						$data['wait'] = 3;
						$data['url'] = site_url('admin/brand/add');
						$this->load->view('message.html',$data);

					}
				}else{
					#上传失败
					$data['message'] = $this->upload->display_errors();
					$data['wait'] = 3;
					$data['url'] = site_url('admin/brand/add');
					$this->load->view('message.html',$data);

				}
			}else{
				#调用品牌模型完成更新动作
				$data['logo'] = $this->input->post('oldLogo');
				if($this->brand_model->updata_brand($brand_id, $data)){
					$data['message'] = '更新品牌成功';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/brand/index');
					$this->load->view('message.html',$data);
				}else{
					$data['message'] = '更新品牌失败';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/brand/add');
					$this->load->view('message.html',$data);

				}
			}
		}
	}

	#搜索品牌
	public function search(){

		$brand_name = $this->input->post('brand_name');
		$data['brands'] = $this->brand_model->search_brand($brand_name);
		$this->load->view('brand_list.html', $data);
	}

	#移除品牌
	public function remove(){
		$brand_id = $this->uri->segment(4);

		$data['goods'] = $this->brand_model->get_brand_good($brand_id);

		if(!empty($data['goods']))
		{
			$data['message'] = '品牌下有商品,请确保改品牌下没有商品';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/brand/index');
			$this->load->view('message.html',$data);
		}else{
			$brand = $this->brand_model->get_brand($brand_id);
			$logo_add = dirname(dirname(dirname(dirname(__FILE__)))).'\\public\brand_img'.'\\'.$brand['logo'];

			if (file_exists($logo_add))
			{
		       if(!unlink($logo_add))
		       {
		       		$data['message'] = '移除品牌失败';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/brand/index');
					$this->load->view('message.html',$data);
		       }
		    }
		    if($this->brand_model->remove($brand_id))
       		{
       			$data['message'] = '移除品牌成功';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/brand/index');
				$this->load->view('message.html',$data);
       		}
       		else
       		{
       			$data['message'] = '移除品牌失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/brand/index');
				$this->load->view('message.html',$data);
       		}
		}
	}
}