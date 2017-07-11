<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品收回站控制器
class Recycle extends Admin_Controller{

	public function __construct(){
		parent:: __construct();
		$this->load->model('goods_model');
		$this->load->model('attribute_model');
		$this->load->model('goods_attr_model');
		
	}

	#显示回收站列表
	public function index(){
		$data['goods'] = $this->goods_model->get_recycle_goods();
		$this->load->view('recycle_list.html', $data);
	}

	#商品加入回收站
	public function put_recycle(){
		$goods_id = $this->uri->segment(4);

		if($this->goods_model->pus_recycle($goods_id)){
			$data['message'] = '移入回收站成功';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/recycle/index');
			$this->load->view('message.html', $data);
		}else{
			$data['message'] = '移入回收站失败';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/goods/show_goods_list');
			$this->load->view('message.html', $data);
		}

	}

	#商品移出回收站
	public function pop_recycle(){
		$goods_id = $this->uri->segment(4);

		if($this->goods_model->pop_recycle($goods_id)){
			$data['message'] = '移出回收站成功';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/goods/show_goods_list');
			$this->load->view('message.html', $data);
		}else{
			$data['message'] = '移出回收站失败';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/recycle/index');
			$this->load->view('message.html', $data);
		}

	}

	#删除商品
	public function del_good($goods_id)
	{
		$this->goods_attr_model->del_all_attr($goods_id);
		$this->goods_model->del_all_img($goods_id);
		$this->goods_model->del_goods($goods_id);

		$data['message'] = '删除商品成功';
		$data['wait'] = 3;
		$data['url'] = site_url('admin/recycle/index');
		$this->load->view('message.html', $data);
	}
}