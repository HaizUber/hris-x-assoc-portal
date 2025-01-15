<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pages extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function apps($page = 'apps')
	{
		if (!file_exists(APPPATH . 'views/apps/' . $page . '.php')) {
			show_404();
		}

		$data['title'] = 'Applications';

		$this->load->view('apps/templates/header', $data);
		$this->load->view('apps/' . $page, $data);
		$this->load->view('apps/templates/footer');
	}

	public function leave_dashboard($page = 'dashboard')
	{
		if (!file_exists(APPPATH . 'views/apps/leave/' . $page . '.php')) {
			show_404();
		}
	
		$data['title'] = 'Leave Management Dashboard';
	
		$this->load->view('apps/templates/header', $data);
		$this->load->view('apps/leave/' . $page, $data);
		$this->load->view('apps/templates/footer');
	}

}
