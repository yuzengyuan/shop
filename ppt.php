<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//幻灯片控制器
class Ppt extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('ppt_model');
		$this->load->library('form_validation');
	}

	//显示幻灯片列表页
	public function show_ppt_list()
	{
		$data['ppts'] = $this->ppt_model->get_all_ppt();
		$this->load->view("ppt_list.html",$data);
	}

	//显示添加幻灯片页
	public function show_add_ppt()
	{
		$this->load->view("add_ppt.html");
	}

	//显示编辑幻灯片页
	public function show_edit_ppt($ppt_id)
	{
		$data['ppt'] = $this->ppt_model->get_ppt($ppt_id);
		$this->load->view("edit_ppt.html",$data);
	}

	//编辑幻灯片
	public function edit_ppt()
	{
	    $ppt_id = $this->input->post("ppt_id");

	    $data['ppt'] = $this->ppt_model->get_ppt($ppt_id);
		if(trim($_FILES["img_file_src"]["name"]) == trim($data['ppt']['ppt_name']) || trim($_FILES["img_file_src"]["name"]) == "")
		{
			$ppt['background_color'] = $this->input->post("img_color");
			$ppt['img_url'] = $this->input->post("img_url");
			$ppt['img_text'] = $this->input->post("img_text");
			$ppt['img_sort'] = $this->input->post("img_sort");
			if($this->ppt_model->update_ppt($ppt_id, $ppt))
			{
				$data1['message'] = '更新幻灯片成功';
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/ppt/show_ppt_list');
				$this->load->view('message.html',$data1);
			}
			else
			{
				$data1['message'] = '更新幻灯片失败';
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/ppt/show_ppt_list');
				$this->load->view('message.html',$data1);
			}
		}
		else
		{
			//幻灯片已改变
			$ppt_name = dirname(dirname(dirname(dirname(__FILE__)))).'\\themes\default\images\afficheimg\ppt'.'\\'.$data['ppt']['ppt_name'];
			if(file_exists($ppt_name))
			{
				if(!unlink($ppt_name))
				{
			       	$data1['message'] = '更新幻灯片失败';  
					$data1['wait'] = 3;
					$data1['url'] = site_url('admin/ppt/show_edit_ppt').'/'.$data['ppt']['ppt_id'];
					$this->load->view('message.html',$data1);
			    }
			}
			
			#设置验证规则
			$this->form_validation->set_rules('img_color','背景颜色','required');

			if($this->form_validation->run() == false)
			{
				#未通过验证
				$data1['message'] = validation_errors();
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/ppt/show_edit_ppt').'/'.$data['ppt']['ppt_id'];
				$this->load->view('message.html',$data1);

			}
			else
			{
				$config['upload_path'] = './themes/default/images/afficheimg/ppt';
				$config['allowed_types'] = 'gif|jpg|png';
				
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('img_file_src'))
				{
					#上传成功,获取文件名
					$fileinfo = $this->upload->data();

					$ppt1['ppt_name'] = $fileinfo['file_name'];
					$ppt1['background_color'] = $this->input->post('img_color');
					#获取表单提交数据
					$ppt1['img_url'] = $this->input->post('img_url');
					$ppt1['img_text'] = $this->input->post('img_text');
					$ppt1['img_sort'] = $this->input->post('img_sort');
					

					#调用品牌模型完成插入动作
					if($this->ppt_model->update_ppt($ppt_id,$ppt1))
					{
						$data1['message'] = '更新幻灯片成功';
						$data1['wait'] = 3;
						$data1['url'] = site_url('admin/ppt/show_ppt_list');
						$this->load->view('message.html',$data1);
					}
					else
					{
						$data1['message'] = '更新幻灯片失败';
						$data1['wait'] = 3;
						$data1['url'] = site_url('admin/ppt/show_edit_ppt').'/'.$data['ppt']['ppt_id'];
						$this->load->view('message.html',$data1);

					}
				}
				else
				{
					#上传失败
					$data1['message'] = $this->upload->display_errors();
					$data1['wait'] = 3;
					$data1['url'] = site_url('admin/ppt/show_edit_ppt').'/'.$data['ppt']['ppt_id'];
					$this->load->view('message.html',$data1);

				}
			}
		}	


	}



	//添加幻灯片
	public function add_ppt()
	{
		#设置验证规则
		$this->form_validation->set_rules('img_color','背景颜色','required');

		if($this->form_validation->run() == false){
			#未通过验证
			$data['message'] = validation_errors();
			$data['wait'] = 3;
			$data['url'] = site_url('admin/ppt/show_add_ppt');
			$this->load->view('message.html',$data);

		}
		else
		{
			$config['upload_path'] = './themes/default/images/afficheimg/ppt';
			$config['allowed_types'] = 'gif|jpg|png';
			
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('img_file_src'))
			{
				#上传成功,获取文件名
				$fileinfo = $this->upload->data();
				$data['ppt_name'] = $fileinfo['file_name'];
				$data['background_color'] = $this->input->post('img_color');
				#获取表单提交数据
				$data['img_url'] = $this->input->post('img_url');
				$data['img_text'] = $this->input->post('img_text');
				$data['img_sort'] = $this->input->post('img_sort');
				

				#调用ppt模型完成添加动作
				if($this->ppt_model->add_ppt($data))
				{
					$data['message'] = '添加幻灯片成功';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/ppt/show_ppt_list');
					$this->load->view('message.html',$data);
				}
				else
				{
					$data['message'] = '添加幻灯片失败';
					$data['wait'] = 3;
					$data['url'] = site_url('admin/ppt/show_add_ppt');
					$this->load->view('message.html',$data);

				}
			}
			else
			{
				#上传失败
				$data['message'] = $this->upload->display_errors();
				$data['wait'] = 3;
				$data['url'] = site_url('admin/ppt/show_add_ppt');
				$this->load->view('message.html',$data);

			}
		}	
	}


	//删除幻灯片
	public function del_ppt($ppt_id)
	{
		$data['ppt'] = $this->ppt_model->get_ppt($ppt_id);
		$ppt_name = dirname(dirname(dirname(dirname(__FILE__)))).'\\themes\default\images\afficheimg\ppt'.'\\'.$data['ppt']['ppt_name'];
		if(file_exists($ppt_name))
		{
			if(!unlink($ppt_name))
			{
		       	$data1['message'] = '删除幻灯片失败';  
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/ppt/show_edit_ppt').'/'.$data['ppt']['ppt_id'];
				$this->load->view('message.html',$data1);
		    }
		}
		else
		{
			if($this->ppt_model->del_ppt($ppt_id))
			{
				$data1['message'] = '删除幻灯片成功';
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/ppt/show_ppt_list');
				$this->load->view('message.html',$data1);
			}
			else
			{
				$data1['message'] = '删除幻灯片失败';
				$data1['wait'] = 3;
				$data1['url'] = site_url('admin/ppt/show_ppt_list');
				$this->load->view('message.html',$data1);

			}
		}
			
	}
		
}