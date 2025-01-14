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
        // Capture form data
        $formData = [
            'empID'        => $this->input->post('empID'),
            'lvaDateFrom'  => $this->input->post('lvaDateFrom'),
            'lvaDateTo'    => $this->input->post('lvaDateTo'),
            'lvaType'      => $this->input->post('lvaType'),
            'lvaReason'    => $this->input->post('lvaReason'),
            'lvaDateFiled' => date('Y-m-d'), // Auto set current date
            'lvaStatus'    => 'PENDING',
            'lvaFiledType' => $this->input->post('lvaFractional') ? 'NF' : 'F', // Set filed type based on fractional leave
        ];
        
        // Validation for Leave Types
        if ($formData['lvaType'] == 'VL') {
            // Validate that VL is filed at least 3 days in advance
            $dateFrom = strtotime($formData['lvaDateFrom']);
            $dateFiled = strtotime($formData['lvaDateFiled']);
            $diff = ($dateFrom - $dateFiled) / (60 * 60 * 24); // Convert to days difference
    
            if ($diff < 3) {
                // If less than 3 days, redirect with an error message
                $this->session->set_flashdata('error', 'Vacation Leave should be filed at least 3 calendar days prior to the date of effectivity');
                redirect('leave/home');
            }
        }

            // Validation for Leave Types - Sick Leave (SL)
    if ($formData['lvaType'] == 'SL') {
        $dateFrom = strtotime($formData['lvaDateFrom']);
        $dateTo = strtotime($formData['lvaDateTo']);
        $duration = ($dateTo - $dateFrom) / (60 * 60 * 24) + 1; // Convert to days (inclusive of the start date)

        if ($duration > 2) {
            // Check if a medical certificate is uploaded
            if (empty($_FILES['medCert']['name'])) {
                $this->session->set_flashdata('error', 'Medical Certificate is required for Sick Leave greater than 2 days');
                redirect('leave/home');
            }
        }
    }
    
        // If the fractional leave checkbox is checked, add start and end time
        if ($this->input->post('lvaFractional')) {
            // Combine the hour and minute for start time and end time
            $startTime = $this->input->post('startTimeHour') . ':' . $this->input->post('startTimeMinute');
            $endTime = $this->input->post('endTimeHour') . ':' . $this->input->post('endTimeMinute');
        
            // Add to form data
            $formData['lvaStartTime'] = $startTime;
            $formData['lvaEndTime'] = $endTime;
        }
        
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
    
    public function viewBalance()
    {
        $data['leaveBalance'] = $this->LeaveModel->getAllLeaveApplications(); 
        $this->load->view('apps/templates/header', $data);  
        $this->load->view('apps/leave/leaveBalance', $data);  
        $this->load->view('apps/templates/footer');  
    }    
    
}

