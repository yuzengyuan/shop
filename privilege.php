<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

#权限控制器
class Privilege extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->helper('captcha');
		$this->load->library('form_validation');

	}

	public function login()
	{
		
		$this->load->view('login.html');
	}

	#生成验证码
	public function code(){
		#调用函数生成验证码
		$vals = array(
			'word_length'=> 1
			);

		$code = create_captcha($vals);

		#将验证码字符串保存到session中
		session_start();
		$_SESSION['captcha'] = $code;
	}

	#处理登录
	public  function signin(){
		#设置验证规则
		$this->form_validation->set_rules('username','用户名','required');
		$this->form_validation->set_rules('password','密码','required');

		#获取表单数据
		$captcha = strtolower($this->input->post('code'));

		session_start();
		#获取session中保存的验证码
		$code = @strtolower($_SESSION['captcha']);

		if($captcha == $code){
			if($this->form_validation->run() == false){
				$data['message'] =  validation_errors();
				$data['url'] = site_url('admin/privilege/login');
				$data['wait'] = 3;
				$this->load->view('message.html', $data);

			}else{
				#验证码正确则需要验证用户名和密码
				$username = $this->input->post('username');
				$password = $this->input->post('password');

				if($username == 'admin' && $password == 123){
					# ok，保存session信息,然后跳转到首页
					$_SESSION['admin'] = $username;
					redirect('admin/main/index');

				}else{
					#error
					$data['url'] = site_url('admin/privilege/login');
					$data['message'] = '用户名和密码错误，请重新填写';
					$data['wait'] = 3;
					$this->load->view('message.html', $data);
					}

			}

			

		}else{
			#验证码不正确，给出相应提示然后返回
			$data['url'] = site_url('admin/privilege/login');
			$data['message'] = '验证码错误，请重新填写';
			$data['wait'] = 3;
			$this->load->view('message.html', $data);
		}
	}

	public function logout(){
		$this->session->unset_userdata('admin');
		$this->session->sess_destroy();

		$data['url'] = site_url('admin/privilege/login');
		$data['message'] = '退出成功';
		$data['wait'] = 3;
		$this->load->view('message.html', $data);
			
	//	redirect('admin/privilege/login');
	}
}