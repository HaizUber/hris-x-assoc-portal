<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuthModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function verify_login($employee_id, $password)
    {
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get('assoc_login');

        if ($query->num_rows() > 0) {
            $user = $query->row();
            // Verify password
            if ($user->pass_word === md5($password)) {
                return $user;
            }
        }
        return false;
    }

    public function logout()
    {
        // Destroy the session
        $this->session->sess_destroy();

        // Redirect to the login page
        redirect('login');
    }
}
