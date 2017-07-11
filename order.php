<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//登陆控制器
class Order extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('order_model');
		$this->load->model('express_model');
		$this->load->library('form_validation');
	}

	//显示订单列表
	public function show_order_list()
	{
		$offset= $this->uri->segment(4);
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/order/show_order_list');
		$config['total_rows'] = $this->order_model->count_order();;
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


		$data['orders'] = $this->order_model->get_all_order($limit, $offset);
		$data['status'] = $this->order_model->get_status();

		
		$this->load->view("order_list.html",$data);
	}

	//显示订单
	public function show_order($order_id)
	{
		$data['order'] = $this->order_model->get_order($order_id);
		$data['express'] = $this->express_model->get_all_express();
		$this->load->view("order.html",$data);
	}

	//显示修改地址
	public function show_edit_address($order_id)
	{
		$data['order'] = $this->order_model->get_order_address($order_id);
		$this->load->view('address.html',$data);
	}


	//添加订单备注
	public function add_msg()
	{
		
		$order_id = $this->input->post('order_id');
		$data['smessage'] = $this->input->post('msg');

		$this->order_model->add_order_msg($order_id,$data);
	}

	//显示修改订单商品信息页
	public function show_order_good($order_id)
	{
		$data['goods'] = $this->order_model->get_order_goods($order_id);
		$data['order_id'] = $order_id;
		$data['sum'] = 0;


		//计算订单总价
		foreach ($data['goods']as $good ) {
			$data['sum'] += $good['goods_total'];
		}

		$this->load->view('revise_order.html', $data);
	}

	//更新订单商品
	public function updata_order_goods()
	{
		$data['good_id'] = $this->input->post('goods_id');
		#设置验证规则
		for ($i=0; $i<count($data['good_id']);$i++) 
		{
			$this->form_validation->set_rules("goods_number[$i]","商品数量","required|integer");
			$this->form_validation->set_rules("goods_price[$i]","商品价格","required|numeric");
		}

		if($this->form_validation->run() == false){
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/order/show_order_good').'/'.$this->input->post('order_id');
			$this->load->view('message.html',$data);

		}
		else
		{
			
			$data['order_id'] = $this->input->post('order_id');
			$data['good_id'] = $this->input->post('goods_id');
			$data['num'] = $this->input->post('goods_number');
			$data['price'] = $this->input->post('goods_price');

			for ($i=0; $i<count($data['good_id']);$i++) 
			{
				$this->order_model->update_order_good($data['good_id'][$i],$data['num'][$i],$data['price'][$i]);
			}

			$this->show_order_good($data['order_id']);
		}
		
	}

	//删除订单商品
	public function del_good($good_id,$order_id)
	{

		$data['good_id'] = $good_id;
		if($this->order_model->del_good($good_id))
		{
			$this->show_order_good($order_id);
		}
		else
		{
			$data['message'] = '删除商品失败';
			$data['url'] = site_url('admin/order/show_order_good').'/'.$order_id;
			$data['wait'] = 3;
			$this->load->view('message.html', $data);
		}
		
	}

	//确认发货
	public function deliver_goods($order_id)
	{
		//获取快递公司
		$data['express'] = $this->input->post("express");

		if($data['express'] == -1)
		{
			$data1['message'] = '请选择寄送快递公司名称';
			$data1['url'] = site_url('admin/order/show_order').'/'.$order_id;
			$data1['wait'] = 3;
			$this->load->view('message.html', $data1);
		}
		else
		{
			$this->form_validation->set_rules("odd","快递单号","required");
			if($this->form_validation->run() == false)
			{
				#未通过验证
				$data1['message'] = validation_errors();
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/order/show_order').'/'.$order_id;
				$this->load->view('message.html',$data1);

			}
			else
			{
				//获取订单号
				$data['odd'] = $this->input->post("odd");
				$data['send_time'] = time();
				$data['pay_time'] = time();
				$data['order_status'] = 2;  //2：表示卖家已发货,等待买家收货
				$data['is_pay'] = 1;  //1：表示卖家已付款
				if($this->order_model->updata_order_deliver($order_id,$data) != 0)
				{
					#未通过验证
					$data1['message'] = '发货成功';
					$data1['wait'] = 3;
					$data1['url'] = site_url('admin/order/show_order_list');
					$this->load->view('message.html',$data1);
				}
				else
				{
					#未通过验证
					$data1['message'] = '更新出错,请联系系统管理员';
					$data1['wait'] = 3;
					$data1['url'] = site_url('admin/order/show_order').'/'.$order_id;
					$this->load->view('message.html',$data1);
				}
			}
		}
	}

	//显示订单查询页面
	public function show_order_check()
	{
		$this->load->view('order_query.html');
	}

	//查询订单
	public function do_order_check()
	{
		#设置验证规则
		$this->form_validation->set_rules('order_sn','订单号','required');


		if($this->form_validation->run() == false)
		{
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/order/show_order_check');
			$this->load->view('message.html',$data);

		}
		else
		{
			$order_num = $this->input->post('order_sn');

			$data['order'] = $this->order_model->get_order_num($order_num);

			if(!empty($data['order']))
			{
	
				$this->load->view("order.html",$data);
			}
			else
			{
				$data1['message'] = "没有此订单!";
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/order/show_order_check');
				$this->load->view('message.html',$data1);
			}
			
		}
	}

	//打印订单
	public function print_order($order_id)
	{
		$data['order'] = $this->order_model->get_order($order_id);
		
		
		if(!empty($data['order']))
		{
			
			$this->load->view("print_order.html",$data);
		}
		else
		{
			
			$data1['message'] = "没有此订单!";
			$data1['wait'] = 3;
			$data1['url'] = site_url('admin/order/show_order_list');
			$this->load->view('message.html',$data1);
		}
		
	}

	//显示发货单列表
	public function show_delivery_list()
	{
		
		$offset= $this->uri->segment(4);
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/order/show_delivery_list');
		$config['total_rows'] = $this->order_model->get_all_delivery_order_count();
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

		
		$data['orders'] = $this->order_model->get_all_delivery_order($limit, $offset);
		
		$this->load->view("order_list.html",$data);
	}

}