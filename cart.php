<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//购物车控制器
class Cart extends Home_Controller{

	public function __construct(){
		parent::__construct();
	}

	public function add_cart()
	{
		$goods_id = $this->input->post('good_id');
		$good_name = $this->input->post('good_name');
		$price = $this->input->post('good_price');
		$number = $this->input->post('good_num');

/*
		$data = array(
               'id'      => 'sku_123ABC',
               'qty'     => 1,
               'price'   => 39.95,
               'name'    => 'T-Shirt',
               'options' => array('Size' => 'L', 'Color' => 'Red')
            );

		$this->cart->insert($data); 
		*/

	//	echo $goods_id;

	}
}