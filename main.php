<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends Admin_Controller{

	#展示后台首页面
	public function index(){
		$this->load->view('index.html');
	}
	#展示头部
	public function top(){
		$this->load->view('top.html');
	}
	#展示菜单
	public function menu(){
		$this->load->view('menu.html');
	}
	#展示分界栏
	public function drap(){
		$this->load->view('drap.html');
	}
	#展示主内容
	public function content(){
		$this->load->view('main.html');
	}

}