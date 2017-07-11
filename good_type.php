<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品类别控制器
class Good_type extends Admin_Controller{

	public function __construct(){
		parent:: __construct();
		$this->load->library('form_validation');
		$this->load->model('good_type_model');
		$this->load->library('pagination');

	}

	public function index($offset=""){
		#配置分页信息
		$config['base_url'] = site_url('admin/good_type/index');
		$config['total_rows'] = $this->good_type_model->count_goodstype();
		$config['per_page'] = 5; 
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

		$limit = $config['per_page'] = 5; 
		$data['goodstype'] = $this->good_type_model->list_goodstype($limit, $offset);
		$this->load->view('goods_type_list.html', $data);
	}

	public function add(){
		$this->load->view('goods_type_add.html');
	}

	public function edit(){
		$type_id = $this->uri->segment(4);
		$data['goodtype'] = $this->good_type_model->get_type($type_id);
		$this->load->view('goods_type_edit.html', $data);
	}

	#更新商品类型
	public function updata(){
		$type_id = $this->input->post('type_id');
		$type_name = $this->input->post('type_name');

		$type_info = array('type_id'=>$type_id, 'type_name'=>$type_name);
		if($this->good_type_model->updata($type_info)){
			$data['message'] = '更新商品类型成功';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/good_type/index');
			$this->load->view('message.html',$data);
		}else{
			$data['message'] = '更新商品类型失败';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/good_type/edit');
			$this->load->view('message.html',$data);
		}
	}

	#添加商品类型
	public function insert(){
		#设置验证规则
		$this->form_validation->set_rules('type_name', '商品类型名称', 'required');

		if ($this->form_validation->run() == false) {
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/good_type/add');
			$this->load->view('message.html',$data);
		}else{

			$data['type_name'] = $this->input->post('type_name', true);

			if ($this->good_type_model->add_goodstype($data)) {
				$data['message'] = '添加商品类型成功';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/good_type/index');
				$this->load->view('message.html',$data);
			}else{
				$data['message'] = '添加商品类型失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/good_type/add');
				$this->load->view('message.html',$data);
			}
		}
	}

	#移除商品类型
	public function remove(){

		$type_id = $this->uri->segment(4);
		if($this->good_type_model->remove($type_id)){
			$data['message'] = '移除商品类型成功';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/good_type/index');
			$this->load->view('message.html',$data);
		}else{
			$data['message'] = '移除商品类型失败';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/good_type/index');
			$this->load->view('message.html',$data);
		}
	}

}