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
        // Load the title for the leave dashboard
        $data['title'] = 'Leave Management';
    
        // Check if the user is an Approving Officer from the session
        $data['isApprovingOfficer'] = $this->session->userdata('isApprovingOfficer');
        
        // Log the value of $isApprovingOfficer
        log_message('debug', 'Is Approving Officer: ' . var_export($data['isApprovingOfficer'], true));
        $employee_id = $this->session->userdata('employee_id');
        $this->load->model('CheckUserRoleModel');
        $data['isHR'] = $this->CheckUserRoleModel->check_hr($employee_id);
        $data['pendingLeaveCount'] = $this->LeaveModel->getPendingLeavesCount($employee_id);
    
        $this->load->view('apps/templates/header', $data);
        $this->load->view('apps/leave/dashboard', $data);  // This will load the dashboard.php view
        $this->load->view('apps/templates/footer');
    }
    
public function viewApprove()
{
    // Load the view for the leave approval page
    $data['title'] = 'Leave Management';
    $data['leaveRecords'] = $this->LeaveModel->getAllLeaveApplications();

    $this->load->view('apps/templates/header', $data);
    $this->load->view('apps/leave/approveleave', $data);  // This will load the ApproveLeave.php view
    $this->load->view('apps/templates/footer');
}

public function viewBalance()
{
    $this->load->model('LeaveModel');

    $data['title'] = 'Leave Balance';
    $employee_id = $this->session->userdata('employee_id');
    
    if (!$employee_id) {
        // If employee ID is not found in the session
        $data['error'] = 'Employee ID not found. Please log in again.';
        $this->load->view('apps/templates/header', $data);
        $this->load->view('apps/leave/leavebalance', $data);
        $this->load->view('apps/templates/footer');
        return;
    }

    $currentDate = date('Y-m-d');

    // Get the current school year
    $schoolYear = $this->LeaveModel->getSchoolYearByDate($currentDate);

    // Validate school year data
    if (!$schoolYear || !isset($schoolYear['start_date']) || !isset($schoolYear['end_date'])) {
        $data['error'] = 'Unable to determine the school year for the current date.';
        $this->load->view('apps/templates/header', $data);
        $this->load->view('apps/leave/leavebalance', $data);
        $this->load->view('apps/templates/footer');
        return;
    }
        // Get the latest school year
        $latestSchoolYear = $this->LeaveModel->getLatestSchoolYear();

        // Pass it to the view
        $data['latestSchoolYear'] = $latestSchoolYear;


    $startYear = date('Y', strtotime($schoolYear['start_date']));
    $endYear = date('Y', strtotime($schoolYear['end_date']));
    $schoolYearRange = "{$startYear}{$endYear}";

    // Get leave balances for the employee
    $leaveBalances = $this->LeaveModel->getLeaveBalancesForEmployee($employee_id, $schoolYearRange);

    if (!$leaveBalances) {
        $data['error'] = 'No leave balance data found for this employee in the current school year.';
        $leaveBalances = [];
    }

    // Initialize variables for used leaves and CDLD count
    $usedSL = 0;
    $usedVL = 0;
    $cdldVlCount = 0;  // Count for CDLD leaves

    // Get the count of CDLD VL leaves for the employee within the current school year range
    $cdldVlCount = $this->LeaveModel->countCdldVlLeavesForEmployee($employee_id, $startYear, $endYear);

    // Get all approved leave records for the employee within the current school year range
    $approvedLeaves = $this->LeaveModel->getApprovedLeavesForEmployee($employee_id, $startYear, $endYear);

    // Loop through each approved leave and compute the used leave
    foreach ($approvedLeaves as $leave) {
        $lvaDays = $leave['lvaDays'];
        $daysUsed = $lvaDays / 8; // Convert hours to days (1 day = 8 hours)

        // Add to used leave (based on the type of leave)
        if ($leave['lvaType'] == 'SL') {
            $usedSL += $daysUsed;
        } elseif ($leave['lvaType'] == 'VL') {
            // Check if lvaReason exists and is not null
            if (isset($leave['lvaReason']) && strtolower($leave['lvaReason']) != 'cdld') {
                $usedVL += $daysUsed;
            }
        }
    }

    // Log the amount of used VL being passed to the view
    log_message('debug', "Used VL (Vacation Leave) being passed to the view: {$usedVL} days");

    // Update the leave balances with the used SL and VL
    if ($leaveBalances) {
        // Update used leave in tblleavebalance
        $this->LeaveModel->updateLeaveBalance($employee_id, $schoolYearRange, $usedSL, $usedVL);

        $leaveBalances['used_SL'] = $usedSL;
        $leaveBalances['used_VL'] = $usedVL;
    }

    // Get user details using the getuserDetails function
    $userDetails = $this->LeaveModel->getuserDetails($employee_id);  // Fetch user details from LeaveModel
    if ($userDetails) {
        $data['userDetails'] = $userDetails;  // Add user details to the data array
    } else {
        $data['error'] = 'Unable to fetch user details.';
    }

    // Pass CDLD VL count to the view
    $data['cdldVlCount'] = $cdldVlCount;
    $data['leaveBalances'] = $leaveBalances;

    // Load the views
    $this->load->view('apps/templates/header', $data);
    $this->load->view('apps/leave/leavebalance', $data);
    $this->load->view('apps/templates/footer');
}

public function submitLeave()
{
    // Get the employee ID from session
    $employeeId = $this->session->userdata('employee_id');

    // Get today's date in the desired format (e.g., Y-m-d)
    $today = date('Ymd');  // This will give the date in the format '20250116'

    // Generate a unique ID for the file
    $uniqueId = uniqid();  // Unique ID generated using PHP's uniqid function

    // Construct the new file name
    $fileName = 'medCert_' . $employeeId . '_' . $today . '_' . $uniqueId;

    // Configuring file upload
    $config['upload_path'] = FCPATH . 'uploads/medcert/'; // FCPATH is CodeIgniter's root path constant
    $config['allowed_types'] = 'jpg|jpeg|png|pdf'; // Allowed file types
    $config['max_size'] = 2048; // Maximum file size (2MB)
    $config['file_name'] = $fileName; // Unique file name

    // Load the upload library and initialize with the config
    $this->load->library('upload');
    $this->upload->initialize($config);

    // Capture form data
    $formData = [
        'empID'        => $this->input->post('empID'),
        'lvaDateFrom'  => $this->input->post('lvaDateFrom'),
        'lvaDateTo'    => $this->input->post('lvaDateTo'),
        'lvaType'      => $this->input->post('lvaType'),
        'lvaReason'    => $this->input->post('lvaReason'),
        'lvaDateFiled' => date('Y-m-d'), // Auto set current date
        'lvaStatus'    => 'PENDING',
        'lvaDays'      => $this->input->post('lvaDays'),
        'lvaFiledType' => $this->input->post('lvaFractional') ? 'NF' : 'F', // Set filed type based on fractional leave
        'lvaMedCert'   => isset($formData['lvaMedCert']) ? $formData['lvaMedCert'] : null // Include medical certificate if uploaded
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
        $duration = ($dateTo - $dateFrom) / (60 * 60 * 24) + 1; // Convert to days

        // Check if the leave duration is greater than 2 days
        if ($duration > 2) {
            // Check if a medical certificate is uploaded
            if (empty($_FILES['medCert']['name'])) {
                $this->session->set_flashdata('error', 'Medical Certificate is required for Sick Leave greater than 2 days');
                redirect('leave/home');
            } else {
                // Handle file upload if medical certificate is provided
                if (!$this->upload->do_upload('medCert')) {
                    // If upload fails, set error message
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    redirect('leave/home');
                } else {
                    // Get the uploaded file data
                    $uploadData = $this->upload->data();
                    // Store the file path in the form data
                    $formData['lvaMedCert'] = $uploadData['file_name'];
                }
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
        $data['leaveRecords'] = $this->LeaveModel->getAllLeaveApplications(); 
        $data['title'] = 'Filed Leave';
        $this->load->view('apps/templates/header', $data);  
        $this->load->view('apps/leave/FiledLeave', $data);  // This will load the filedleave view
        $this->load->view('apps/templates/footer');  
    }
    
    public function cancelLeave($filedNo)
    {
        // Sanitize and validate the filedNo
        $filedNo = htmlspecialchars(strip_tags($filedNo)); 
    
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
        // Sanitize input
        $filedNo = htmlspecialchars(strip_tags($filedNo));
        if (empty($filedNo)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid leave request.']);
            return;
        }
    
        // Get the JSON payload from the request body
        $data = json_decode($this->input->raw_input_stream, true);  // Decode the JSON data
    
        // Check if the comment is set
        $comment = isset($data['comment']) ? $data['comment'] : '';
        if (empty($comment)) {
            echo json_encode(['status' => 'error', 'message' => 'Comment is required.']);
            return;
        }
    
        $this->load->model('LeaveModel');
    
        // Get the current logged-in user's employee_id
        $employeeId = $this->session->userdata('employee_id');
        if (empty($employeeId)) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in or session expired.']);
            return;
        }
    
        // Load the associates database and fetch approver details
        $this->associates_db = $this->load->database('associates', TRUE);
        $this->associates_db->select('first_name, last_name');
        $this->associates_db->from('associates');
        $this->associates_db->where('employee_id', $employeeId);
        $query = $this->associates_db->get();
        $user = $query->row();
    
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve approver details.']);
            return;
        }
    
        // Combine first_name and last_name to form the full name
        $approvedBy = $user->first_name . ' ' . $user->last_name;
    
        // Approve the leave in the database with the comment and approvedBy info
        $result = $this->LeaveModel->approveLeave($filedNo, $approvedBy, $comment);
    
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

    public function getPendingLeaveCount() {
        $this->load->model('LeaveModel');
        $pendingCount = $this->LeaveModel->getPendingLeavesCount();
        return $pendingCount;
    }
        
}

