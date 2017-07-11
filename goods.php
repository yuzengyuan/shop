<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品控制器
class Goods extends Admin_Controller{

	public function __construct(){
		parent:: __construct();
		$this->load->model('good_type_model');
		$this->load->model('attribute_model');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->load->model('goods_model');
		$this->load->model('goods_attr_model');
		$this->load->library('form_validation');
		$config['upload_path'] = './public/goods_img/';
		$config['allowed_types'] = 'gif|jpg|png';

		$this->load->library('upload', $config);

	}

	#显示商品列表
	public function show_goods_list(){

		$offset= $this->uri->segment(4);

		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/goods/show_goods_list');
		$config['total_rows'] = $this->goods_model->get_count();
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


		$data['goods'] = $this->goods_model->get_page_goods($limit, $offset);
		$this->load->view('goods_list.html', $data);
	}

	#添加新商品
	public function add(){
		#获取所有的商品类型信息

		$data['goodstypes'] = $this->good_type_model->get_all_types();

		#获取分类信息
		$data['cates'] = $this->category_model->list_cate();

		#获取品牌信息
		$data['brands'] = $this->brand_model->list_brand();

		$this->load->view('goods_add.html', $data);
	}

	#插入商品
	public function insert()
	{
		$this->form_validation->set_rules('goods_name', '商品名字', 'required');
		$this->form_validation->set_rules('goods_sn', '商品货号', 'required');
		$this->form_validation->set_rules('shop_price', '本店价格', 'required');
		$this->form_validation->set_rules('goods_img_url', '上传商品图片', 'required');


		if($this->form_validation->run() == false)
		{
		  
		   	$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/goods/add');
			$this->load->view('message.html',$data);
	
		}
		else
		{
			#获取提交的商品信息
			$data['goods_name'] = $this->input->post('goods_name');                          //商品名字
			$data['goods_sn'] = $this->input->post('goods_sn');      						 //商品货号
			$data['cat_id'] = $this->input->post('cat_id');                                  //商品所属类别ID
			$data['brand_id'] = $this->input->post('brand_id');							     //商品所属品牌ID	
			$data['shop_price'] = $this->input->post('shop_price');							 //本店价格
			$data['market_price'] = $this->input->post('market_price');			             //市场价格			
			$data['promote_price'] = $this->input->post('promote_price');                    //促销价格
			$data['is_promote'] = $this->input->post('is_promote');							 	//是否促销，默认为0不促销
			$data['promote_start_time'] = strtotime($this->input->post('promote_start_time'));          //促销起始时间
			$data['promote_end_time'] = strtotime($this->input->post('promote_end_time'));				 //促销截止时间
			$data['goods_img'] = $this->input->post('goods_img_url');	  				     //商品图片
			$data['goods_desc'] = $this->input->post('goods_desc');							 //商品详情
			$data['goods_weight'] = $this->input->post('goods_weight');                      //商品重量
			$data['goods_number'] = $this->input->post('goods_number');						 //库存数量
			$data['warn_number'] = $this->input->post('warn_number');						 //库存警告数量
			$data['is_best'] = $this->input->post('is_best');								 //是否精品,默认为0
			$data['is_new'] = $this->input->post('is_new');									 //是否新品，默认为0
			$data['is_hot'] = $this->input->post('is_hot');									 //是否热卖,默认为0
			$data['is_onsale'] = $this->input->post('is_onsale');							 //是否上架,默认为1
			$data['keywords'] = $this->input->post('keywords');								 //商品关键词
			$data['goods_brief'] = $this->input->post('goods_brief');						 //商品简单描述

			$data['type_id'] = $this->input->post('goods_type');							 //商品类型ID
			$data['add_time']= time();							 							 //商品添加时间


			if ($goods_id = $this->goods_model->add_goods($data)) 
			{

				#添加商品成功，获取属性并插入到商品属性关联表中
				$attr_ids = $this->input->post('attr_id_list');
				$attr_values = $this->input->post('attr_value_list');
				if(!empty($attr_values))
				{
					foreach ($attr_values as $k => $v) 
					{
						if(!empty($v))
						{
							$data2['goods_id'] = $goods_id;
							$data2['attr_id'] = $attr_ids[$k];
							$data2['attr_value'] = $v;
							$data2['type_id'] = $data['type_id'];
							#完成插入
							$this->db->insert('goods_attr', $data2);
						}
					}
				}
				//插入商品图片
				$goodImgs = $this->input->post('imgs');
				foreach ($goodImgs as $img) 
				{
					if(!empty($img))
					{
						$this->goods_model->add_goods_img($goods_id,$img);
					}
				}
				$data1['message'] = '添加商品成功';
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/goods/show_goods_list');
				$this->load->view('message.html', $data1);
				
			}
			else
			{
				#添加商品失败
				$data1['message'] = '添加商品失败';
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/goods/add');
				$this->load->view('message.html', $data1);
			}
	
		}	
	}


	#更新商品
	public function updata()
	{
		
		$this->form_validation->set_rules('goods_name', '商品名字', 'required');
		$this->form_validation->set_rules('goods_sn', '商品货号', 'required');
		$this->form_validation->set_rules('shop_price', '本店价格', 'required');
		$this->form_validation->set_rules('goods_img_url', '上传商品图片', 'required');

		#验证表单
		if($this->form_validation->run() == false)
		{
		   	$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/goods/show_goods_list');
			$this->load->view('message.html',$data);
	
		}
		else
		{
			#获取提交的商品信息
			$goods_id = $this->input->post('goods_id');
			$data['goods_name'] = $this->input->post('goods_name');                          //商品名字
			$data['goods_sn'] = $this->input->post('goods_sn');      						 //商品货号
			$data['cat_id'] = $this->input->post('cat_id');                                  //商品所属类别ID
			$data['brand_id'] = $this->input->post('brand_id');							     //商品所属品牌ID	
			$data['shop_price'] = $this->input->post('shop_price');							 //本店价格
			$data['market_price'] = $this->input->post('market_price');			             //市场价格			
			$data['promote_price'] = $this->input->post('promote_price');                    //促销价格
			$data['is_promote'] = $this->input->post('is_promote');							 	//是否促销，默认为0不促销
			$data['promote_start_time'] = strtotime($this->input->post('promote_start_time'));          //促销起始时间
			$data['promote_end_time'] = strtotime($this->input->post('promote_end_time'));				 //促销截止时间
			$data['goods_img'] = $this->input->post('goods_img_url');	  					 //商品图片
			$data['goods_desc'] = $this->input->post('goods_desc');							 //商品详情
			$data['goods_weight'] = $this->input->post('goods_weight');                      //商品重量

			$data['goods_number'] = $this->input->post('goods_number');						 //库存数量
			$data['warn_number'] = $this->input->post('warn_number');						 //库存警告数量
			$data['is_best'] = $this->input->post('is_best');								 //是否精品,默认为0
			$data['is_new'] = $this->input->post('is_new');									 //是否新品，默认为0
			$data['is_hot'] = $this->input->post('is_hot');									 //是否热卖,默认为0
			$data['is_onsale'] = $this->input->post('is_onsale');							 //是否上架,默认为1
			$data['keywords'] = $this->input->post('keywords');								 //商品关键词
			$data['goods_brief'] = $this->input->post('goods_brief');						 //商品简单描述
			$data['type_id'] = $this->input->post('goods_type');							 //商品类型ID
			$data['add_time']= time();				 					         //商品添加时间



			//获取要更新商品的ID
			$data1['goods'] = $this->goods_model->get_good($goods_id);

			//获取要更新的商品类型
			$attr_value = $this->goods_attr_model->get_goods_attr1($goods_id);

			$count=0;
			if ($this->goods_model->updata_goods($goods_id,$data)) 
			{
				#添加商品成功，获取属性并插入到商品属性关联表中
				$attr_ids = $this->input->post('attr_id_list');
				$attr_values = $this->input->post('attr_value_list');
	
				if(!empty($attr_values))
				{
					foreach ($attr_values as $k => $v) 
					{
		
						if(!empty($v))
						{
							$data2['goods_id'] = $goods_id;
							$data2['attr_id'] = $attr_ids[$k];
							$data2['attr_value'] = $v;
							$data2['type_id'] = $data['type_id'];
					
							#完成插入
							if($attr_value[$count]['attr_value'] != $data2['attr_value'])
							{
								#完成插入
								$this->db->where('attr_value', $attr_value[$count]['attr_value'])->update('goods_attr', $data2);
							}
						}
						$count++;
					}
				}
	

				if($this->goods_model->del_goods_imgs($goods_id))
				{
					$goodImgs = $this->input->post('imgs');
					foreach ($goodImgs as $img) 
					{
						if(!empty($img))
						{
							$this->goods_model->add_goods_img($goods_id,$img);
						}
					}

					$data1['message'] = '更新商品成功';
					$data1['wait'] = 3;
					$data1['url'] = site_url('admin/goods/show_goods_list');
					$this->load->view('message.html', $data1);
				}
				else
				{
					#添加商品失败
					$data['message'] = '更新商品失败';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/goods/add');
					$this->load->view('message.html', $data);
				}

			} 
			else 
			{
				#添加商品失败
				$data['message'] = '更新商品失败';
				$data['wait'] = 3;
				$data['url'] = site_url('admin/goods/add');
				$this->load->view('message.html', $data);
			}
			
		}
	}
	#编辑商品
	public function edit(){

		$goods_id = $this->uri->segment(4);

		#获取指定商品
		$data['good'] = $this->goods_model->get_good($goods_id);
		#获取指定分类信息
		$data['cates'] = $this->category_model->list_cate();
		#获取指定品牌信息
		$data['brands'] = $this->brand_model->list_brand();
		#获取指定商品类型信息
		$data['goodstypes'] = $this->good_type_model->get_all_types();	
		#获取商品图片
		$data['goodsimgs'] = $this->goods_model->get_goods_img($goods_id);	
	

		$this->load->view('goods_edit.html', $data);
	}


	#ajax开始
	public function create_attrs_html()
	{

		#获取类型id
		$type_id = $this->input->post('type_id');
		$goods_id = $this->input->post('goods_id');
		if($type_id != 0)
		{
			#查询商品属性
			$attrs = $this->attribute_model->get_attrs($type_id);
		

			#查询商品属性值
			$attr_value = $this->goods_attr_model->get_goods_attr1($goods_id);

			$attrs = array();
			foreach ($attr_value as $attr)
			{
				$arr_name = $this->attribute_model->get_attrs1($attr['attr_id']);

				$arr_name[] = $attr['attr_value'];
				$attrs[]= $arr_name;
	
			}



			#标记是否第一次进入
			$state_fist_enter = $this->input->post('state_fist_enter');

			#如果属性值不等于空 并且 商品类型跟编辑进入的的商品类型等同
			if(!empty($attr_value) && ($state_fist_enter == $type_id))
			{
				#根据获取到的属性值构造html字符串
				$html = '';

				foreach($attrs as $v){
					
					$html .= "<tr>";
					$html.= "<td class='label'>".$v['attr_name']."</td>";
					$html .= "<td>";
					$html .= "<input type='hidden' name='attr_id_list[]'' value='".$v['attr_id']."'>";

					static $count = 0;
					switch($v['attr_input_type']){

						case 0:
							#文本框
							@$html .="<input name='attr_value_list[]' type='text' size='40'  value='".$attr_value[$count]['attr_value']."'>";
							$count++;
							break;

						case 1:
							
							#下拉列表
							$arr = explode(" ", $v['attr_value']);
							$html .= "<select name='attr_value_list[]'>";

							$html .= "<option value=''> 请选择...</option>";

							foreach ($arr as $v ) {
								if(($attr_value[$count]['attr_value']) != $v)
								{
									$html .= "<option value='$v'>$v</option>";
								}
								else
								{
									$html .= "<option value='$v' selected>$v</option>";
								}
							}
							$html .="</select>";
							$count++;
							break;

						case 2:
							#文本域
							break;

						default:
							break;
					}
					
					$html.="</td>";
					$html.="</tr>";
				}
			}else{

				#根据获取到的属性值构造html字符串
				$html = '';
				//获取商品类型名字
				$attrs = $this->attribute_model->get_attrs($type_id);
				foreach($attrs as $v){
		
					$html .= "<tr>";
					$html.= "<td class='label'>".$v['attr_name']."</td>";
					$html .= "<td>";
					$html .= "<input type='hidden' name='attr_id_list[]'' value='".$v['attr_id']."'>";

					switch($v['attr_input_type']){

						case 0:
							#文本框
							$html .="<input name='attr_value_list[]' type='text' size='40'>";
							break;

						case 1:
							
							#下拉列表
							$arr = explode(" ", $v['attr_value']);
							$html .= "<select name='attr_value_list[]'>";

							$html .= "<option value=''> 请选择...</option>";

							foreach ($arr as $v ) {
								$html .= "<option value='$v'>$v</option>";
							}
							$html .="</select>";
							break;

						case 2:
							#文本域
							break;

						default:
							break;
					}
					
					$html.="</td>";
					$html.="</tr>";
				}
			}

			echo $html;
		}
	}

}