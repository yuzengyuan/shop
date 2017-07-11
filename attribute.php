<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品属性控制器
class Attribute extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		
		$this->load->model('good_type_model');
		$this->load->model('attribute_model');
		$this->load->library('pagination');
	}

	public function index(){

		#获取类型ID
		$id = $this->uri->segment(4);
		#获取分页段数
		$offset= $this->uri->segment(5);
		#获取选中类型
		$type = $this->input->post('type_name');

		if($type != ""){
			$id = $type;
			$offset = "";
		}

		#配置分页信息
		$config['base_url'] = site_url('admin/attribute/index/'.$id);
		$config['per_page'] = 5; 
		$config['uri_segment'] = 5;
		$config['total_rows'] = $this->attribute_model->count_attrstype($id);

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


		#获取某类型属性
		$data['attrs'] = $this->attribute_model->list_get_attrs($id,$limit, $offset);

		#获取所有商品类型
		$data['goodstypes'] = $this->good_type_model->get_all_types();
		#分配视图
		$this->load->view('attribute_list.html', $data);
	}

	public function add(){
		#获取商品类型信息
		$data['goodstypes'] = $this->good_type_model->get_all_types();
		$this->load->view('attribute_add.html', $data);
	}

	public function edit(){
		#获取商品属性类型id
		$attr_id = $this->uri->segment(4);
		$data['goodstypes'] = $this->good_type_model->get_all_types();
		$data['attr'] = $this->attribute_model->get_attr($attr_id);
		$this->load->view('attribute_edit.html',$data);

	}

	public function updata(){
		$attr_id = $this->input->post('attr_id');

		$data['attr_name'] = $this->input->post('attr_name');
		$data['type_id'] = $this->input->post('cat_id');
		$data['attr_type'] = $this->input->post('attr_type');
		$data['attr_input_type'] = $this->input->post('attr_input_type');
		$data['attr_value'] = $this->input->post('attr_value');
		$data['sort_order'] = $this->input->post('sort_order');

		if ($this->attribute_model->updata($attr_id, $data)) {
			# ok
			$data['message'] = '更新属性成功';
			$data['url'] = site_url('admin/attribute/index').'/'.$data['type_id'];;
			$data['wait'] = 3;
			$this->load->view('message.html', $data);
		}
		else{
			#error
			$data['message'] = '更新属性失败';
			$data['url'] = site_url('admin/attribute/index').'/'.$data['type_id'];;
			$data['wait'] = 3;
			$this->load->view('message.html', $data);
		}
	}

	#添加属性
	public function insert(){

		$data['attr_name'] = $this->input->post('attr_name');
		$data['type_id'] = $this->input->post('cat_id');
		$data['attr_type'] = $this->input->post('attr_type');
		$data['attr_input_type'] = $this->input->post('attr_input_type');
		$data['attr_value'] = $this->input->post('attr_value');
		$data['sort_order'] = $this->input->post('sort_order');

		if ($this->attribute_model->add_attrs($data)) {
			# ok
			$data['message'] = '添加属性成功';
			$data['url'] = site_url('admin/attribute/index');
			$data['wait'] = 3;
			$this->load->view('message.html', $data);
		}
		else{
			#error
			$data['message'] = '添加属性失败';
			$data['url'] = site_url('admin/attribute/add');
			$data['wait'] = 3;
			$this->load->view('message.html', $data);
		}
	}

	#移除属性
	public function remove(){

		$attr_id = $this->uri->segment(4);
		$type_id = $this->uri->segment(5);
		if($this->attribute_model->remove($attr_id)){
			$data['message'] = '移除属性成功';
			$data['url'] = site_url('admin/attribute/index').'/'.$type_id;
			$data['wait'] = 3;
			$this->load->view('message.html', $data);
		}else{
			$data['message'] = '移除属性失败';
			$data['url'] = site_url('admin/attribute/index').'/'.$type_id;
			$data['wait'] = 3;
			$this->load->view('message.html', $data);
		}
	}
}