<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaseController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AuthModel');
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function index($page = 'index')
    {
        if (!file_exists(APPPATH . 'views/auth/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'Sign in - APF';

        $this->load->view('auth/' . $page, $data);
    }

    public function login()
    {
        $employee_id = $this->input->post('username');
        $password = $this->input->post('password');

        // Validate credentials
        $user = $this->AuthModel->verify_login($employee_id, $password);

        if ($user) {
            // Store user data in session
            $this->session->set_userdata([
                'employee_id' => $user->employee_id,
                'user_name' => $user->user_name,
                'logged_in' => true
            ]);

            redirect('apps'); // Redirect to apps page
        } else {
            $this->session->set_flashdata('error', 'Invalid Employee ID or Password');
            redirect(''); // Redirect back to login page
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(''); // Redirect to login page
    }
}
