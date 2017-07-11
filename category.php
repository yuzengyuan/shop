<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
* 商品类别控制器
*/

class Category extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('category_model');
		$this->load->model('goods_model');
		$this->load->library('form_validation');
	}

	/* 插入几条测试数据
	insert into ci_category(cat_name,parent_id)values('广东'，0);
	insert into ci_category(cat_name,parent_id)values('湖北',0);
	insert into ci_category(cat_name,parent_id)values('中山',1);
	insert into ci_category(cat_name,parent_id)values('武汉',2);
	insert into ci_category(cat_name,parent_id)values('顺德',3);
	insert into ci_category(cat_name,parent_id)values('武昌',4);
	*/
	#显示分类信息
	public function index(){
		$data['cates'] = $this->category_model->list_cate();

		$data['numbers'] = $this->goods_model->get_cat_all($data['cates']);
		$this->load->view('cat_list.html',$data);

	}

	#显示添加表单
	public function add(){
		$data['cates'] = $this->category_model->list_cate();
		$this->load->view('cat_add.html',$data);
	}

	#显示编辑表单
	public function edit($cat_id){
		#获取所有的分类信息
		$data['cates'] = $this->category_model->list_cate();

		#获取$cat_id
	//	$cat_id = $this->uri->segment(4);
		$data['current_cat'] = $this->category_model->get_cate($cat_id);
		$this->load->view('cat_edit.html',$data);
	}

	#完成添加分类动作
	public function insert(){
		#设置验证规则
		$this->form_validation->set_rules('cat_name','分类名称','trim|required');
		if($this->form_validation->run() == false){
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/category/add');
			$this->load->view('message.html', $data);
		}else{
			#通过验证
			$data['cat_name'] = $this->input->post('cat_name',true);
			$data['parent_id'] = $this->input->post('parent_id');
			$data['unit'] = $this->input->post('measure_unit',true);
			$data['sort_order'] = $this->input->post('sort_order',true);
			$data['is_show'] = $this->input->post('is_show');
			$data['is_nav']= $this->input->post('is_nav');
			$data['cat_desc'] = $this->input->post('cat_desc',true);

			#调用model完成插入
			if ($this->category_model->add_category($data)){
				#插入ok
				$data['message'] = '添加商品类别成功';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/category/add');
				$this->load->view('message.html', $data);
			}else{
				#插入失败
				$data['message'] = '添加商品类别失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/category/add');
				$this->load->view('message.html', $data);
			}
		}
	}

	#更新操作
	public function update(){
		$cat_id = $this->input->post('cat_id');

		#获取该cat_id 分类下的所有后代分类
		$sub_cates = $this->category_model->list_cate($cat_id);
		#获取这些后代分类的cat_id
		$sub_ids = array();

		foreach($sub_cates as $v){
			$sub_ids[] = $v['cat_id'];
		}

		$parent_id = $this->input->post('parent_id');
		#判断当前所选的父分类是否为当前分类或其后代分类
		if($parent_id == $cat_id || in_array($parent_id, $sub_ids)){

			$data['message'] = '不能将分类放置到当前分类或其子分类';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/category/edit').'/'.$cat_id;
			$this->load->view('message.html', $data);
		}else{
			#进行数据更新
			$data['cat_name'] = $this->input->post('cat_name',true);
			$data['parent_id'] = $this->input->post('parent_id');
			$data['unit'] = $this->input->post('measure_unit',true);
			$data['sort_order'] = $this->input->post('sort_order',true);
			$data['is_show'] = $this->input->post('is_show');
			$data['is_nav']= $this->input->post('is_nav');
			$data['cat_desc'] = $this->input->post('cat_desc',true);


			if($this->category_model->update_cate($data,$cat_id)){
				$data['message'] = '更新成功';
				$data['wait'] = 1;
				$data['url'] = site_url('admin/category/index');
				$this->load->view('message.html', $data);
			}else{
				$data['message'] = '更新失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/category/edit').'/'.$cat_id;
				$this->load->view('message.html', $data);
			}
		}
	}


	#移除分类
	function remove(){
		$cat_id = $this->uri->segment(4);
		$cate = $this->category_model->get_cate($cat_id);
		#判断分类下面有子分类否
		$is_false = $this->category_model->is_son_cats($cat_id);

/*
		#如果分类不是父级分类
		if($cate['parent_id'] != 0)
		{
			if($is_false == true){
				$data['message'] = '分类下有子分类,请确保没有子分类';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/category/index');
				$this->load->view('message.html', $data);
			}
			else{
				$goods = $this->goods_model->get_goods($cat_id);
				#判断分类是否有商品,1表示有商品
				if(empty($goods) == 1)
				{
					#如果移除失败,返回false
					if(!$this->category_model->remove($cat_id)){
						$data['message'] = '移除失败,请联系编码人员';
						$data['wait'] = 3;
						$data['url'] = site_url('admin/category/index');
						$this->load->view('message.html', $data);
					}else{
						$data['message'] = '移除成功';
						$data['wait'] = 3;
						$data['url'] = site_url('admin/category/index');
						$this->load->view('message.html', $data);
					}
				}
				else
				{
					$data['message'] = '分类下面有商品,请确保没有商品';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/category/index');
					$this->load->view('message.html', $data);
				}
			}
		}
		else
		{
			$data['message'] = '分类下有子分类,请确保没有子分类';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/category/index');
			$this->load->view('message.html', $data);
		}
*/


		#判断分类是否有子分类
		if($is_false == false)
		{
			$goods = $this->goods_model->get_goods($cat_id);
			#判断分类是否有商品,1表示有商品
			if(empty($goods) == 1)
			{
				#如果移除失败,返回false
				if(!$this->category_model->remove($cat_id)){
					$data['message'] = '移除失败,请联系编码人员';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/category/index');
					$this->load->view('message.html', $data);
				}else{
					$data['message'] = '移除成功';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/category/index');
					$this->load->view('message.html', $data);
				}
			}
			else
			{
				$data['message'] = '分类下面有商品,请确保没有商品';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/category/index');
				$this->load->view('message.html', $data);
			}
		
		}
		else
		{
			$data['message'] = '分类下有子分类,请确保没有子分类';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/category/index');
			$this->load->view('message.html', $data);
		}

	}

	#转移商品显示页
	public function shiftGoods(){
		$data['id'] = $this->uri->segment(4);
		$data['cates'] = $this->category_model->list_cate();
		$this->load->view('move_cat_goods.html', $data);

	}

	#转移商品动作
	public function moveGoods(){
		$cat_id = $this->input->post('cat_id');
		$target_cat_id = $this->input->post('target_cat_id');

		#判读移动商品是否符合规则
		if($cat_id == 0 || $target_cat_id == 0){
			$data['message'] = '分类不能是顶级分类';
			$data['wait'] = 3;
			$data['url'] = site_url('admin/category/shiftGoods');
			$this->load->view('message.html', $data);
		}else
		{
			#开始移动商品
			if($this->goods_model->updata_goods_cat($cat_id, $target_cat_id)){
				$data['message'] = '转移商品成功';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/category/index');
				$this->load->view('message.html', $data);
			}else{
				$data['message'] = '转移商品失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/category/index');
				$this->load->view('message.html', $data);
			}
		}
	}

}