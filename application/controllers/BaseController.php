<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaseController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index($page = 'index')
	{
		if (!file_exists(APPPATH . 'views/auth/' . $page . '.php')) {
			show_404();
		}

		$data['title'] = 'Sign in - APF';

		$this->load->view('auth/' . $page, $data);
	}
}
