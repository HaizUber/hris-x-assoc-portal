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
            // Check if the user is an Approving Officer by querying tblapprovingofficer table
            $this->load->model('CheckUserRoleModel');  // Load the model for checking the tblapprovingofficer table
            
            // Query the tblapprovingofficer table to check if the employee_id exists
            $isApprovingOfficer = $this->CheckUserRoleModel->check_approving_officer($user->employee_id);
    
            // Store user data in session, including the isApprovingOfficer flag
            $this->session->set_userdata([
                'employee_id' => $user->employee_id,
                'user_name' => $user->user_name,
                'logged_in' => true,
                'isApprovingOfficer' => $isApprovingOfficer, // Store this flag in the session
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
