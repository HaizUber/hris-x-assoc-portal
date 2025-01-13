<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LeaveController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Load the LeaveModel globally for all methods
        $this->load->model('Apps/LeaveModel');
        $this->load->library('session');
    }

    public function index()
    {
        // Fetch leave types from the model
        $data['leaveTypes'] = $this->LeaveModel->getLeaveTypes();

        // Load the views with leave types
        $data['title'] = 'Leave Management';

        $this->load->view('apps/templates/header', $data);
        $this->load->view('apps/leave/home', $data);
        $this->load->view('apps/templates/footer');
    }

    public function home()
    {
        $this->index();
    }

    public function submitLeave()
    {
        // Fetch form data
        $formData = [
            'empID'       => $this->input->post('empID'),
            'lvaDateFrom' => $this->input->post('lvaDateFrom'),
            'lvaDateTo'   => $this->input->post('lvaDateTo'),
            'lvaType'     => $this->input->post('lvaType'),
            'lvaReason'   => $this->input->post('lvaReason'),
            'lvaDateFiled' => date('Y-m-d'), // Auto set current date
            'lvaStatus'   => 'PENDING',
        ];

        // Insert the leave data into the database
        if ($this->LeaveModel->fileLeave($formData)) {
            // Redirect with a success message
            $this->session->set_flashdata('success', 'Leave successfully filed!');
        } else {
            // Redirect with an error message
            $this->session->set_flashdata('error', 'Failed to file leave. Please try again.');
        }

        redirect('leave/home');
    }
}

