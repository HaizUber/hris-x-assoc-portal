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

    public function dashboard()
{
    // Load the view for the leave dashboard
    $data['title'] = 'Leave Management';

    $this->load->view('apps/templates/header', $data);
    $this->load->view('apps/leave/dashboard', $data);  // This will load the dashboard.php view
    $this->load->view('apps/templates/footer');
}

public function viewApprove()
{
    // Load the view for the leave dashboard
    $data['title'] = 'Leave Management';
    $data['leaveBalance'] = $this->LeaveModel->getAllLeaveApplications();

    $this->load->view('apps/templates/header', $data);
    $this->load->view('apps/leave/approveleave', $data);  // This will load the ApproveLeave.php view
    $this->load->view('apps/templates/footer');
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
        
    // Convert date strings to timestamps for comparison
    $dateFrom = strtotime($formData['lvaDateFrom']);
    $dateTo = strtotime($formData['lvaDateTo']);
    $dateFiled = strtotime($formData['lvaDateFiled']);
    
    // Validation for future date (leave date should not be in the past)
    if ($dateFrom < $dateFiled || $dateTo < $dateFiled) {
        // If the leave date is in the past, redirect with an error message
        $this->session->set_flashdata('error', 'Leave dates cannot be in the past');
        redirect('leave/home');
    }

    // Validation for correct date range (from date should not be later than to date)
    if ($dateFrom > $dateTo) {
        // If the from date is after the to date, redirect with an error message
        $this->session->set_flashdata('error', 'Leave "From" date cannot be later than the "To" date');
        redirect('leave/home');
    }
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
    
    // Validation for fractional leave time (start time should not be after end time)
    if ($this->input->post('lvaFractional')) {
        $startTimeHour = $this->input->post('startTimeHour');
        $startTimeMinute = $this->input->post('startTimeMinute');
        $endTimeHour = $this->input->post('endTimeHour');
        $endTimeMinute = $this->input->post('endTimeMinute');
        
        // Convert the start and end times to minutes since midnight
        $startTotalMinutes = ($startTimeHour * 60) + $startTimeMinute;
        $endTotalMinutes = ($endTimeHour * 60) + $endTimeMinute;
        
        // Ensure that the start time is earlier than the end time
        if ($startTotalMinutes >= $endTotalMinutes) {
            $this->session->set_flashdata('error', 'Start time cannot be later than or equal to end time for fractional leave');
            redirect('leave/home');
        }
        
        // Add to form data
        $startTime = $startTimeHour . ':' . $startTimeMinute;
        $endTime = $endTimeHour . ':' . $endTimeMinute;
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
    
    public function viewLeave()
    {
        $data['leaveBalance'] = $this->LeaveModel->getAllLeaveApplications(); 
        $data['title'] = 'Filed Leave';
        $this->load->view('apps/templates/header', $data);  
        $this->load->view('apps/leave/FiledLeave', $data);  
        $this->load->view('apps/templates/footer');  
    }
    
    public function cancelLeave($filedNo)
    {
        // Sanitize and validate the filedNo
        $filedNo = htmlspecialchars(strip_tags($filedNo));  // Basic sanitization, improve if needed
    
        if (empty($filedNo)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid leave request.']);
            return;
        }
    
        // Call the model's delete method
        $result = $this->LeaveModel->deleteLeave($filedNo);
    
        if ($result) {
            // Return success response as JSON
            echo json_encode(['status' => 'success', 'message' => 'Leave record cancelled successfully.']);
        } else {
            // Return failure response as JSON
            echo json_encode(['status' => 'error', 'message' => 'Failed to cancel the leave record.']);
        }
    }
    
    public function approveLeave($filedNo)
    {
        // Sanitize and validate the filedNo
        $filedNo = htmlspecialchars(strip_tags($filedNo));  // Basic sanitization, improve if needed
    
        if (empty($filedNo)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid leave request.']);
            return;
        }
    
        // Call the model's approve method
        $result = $this->LeaveModel->approveLeave($filedNo);
    
        if ($result) {
            // Return success response as JSON
            echo json_encode(['status' => 'success', 'message' => 'Leave request approved successfully.']);
        } else {
            // Return failure response as JSON
            echo json_encode(['status' => 'error', 'message' => 'Failed to approve the leave request.']);
        }
    }

    public function disapproveLeave($filedNo)
    {
        // Get the raw POST data
        $json_input = file_get_contents('php://input');
        // Decode the JSON input
        $input_data = json_decode($json_input, true);
        
        // Log the input data to check if we are receiving it correctly
        log_message('info', 'Received data: ' . print_r($input_data, true));
        
        // Validate the data
        $filedNo = htmlspecialchars(strip_tags($filedNo));  // Basic sanitization, improve if needed
        $comment = isset($input_data['comment']) ? $input_data['comment'] : '';  // Get the comment
    
        // Log the comment
        log_message('info', 'Received comment: ' . $comment . ' for filedNo: ' . $filedNo);
    
        if (empty($filedNo) || empty($comment)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid leave request or comment.']);
            return;
        }
    
        // Call the model's disapprove method
        $result = $this->LeaveModel->disapproveLeave($filedNo, $comment);
    
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Leave request disapproved successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to disapprove the leave request.']);
        }
    }
    
        
    
}

