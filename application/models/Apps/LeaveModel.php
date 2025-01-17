<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LeaveModel extends CI_Model
{
    private $associates_db;

    public function __construct()
    {
        parent::__construct();

        // Load the associates database connection
        $this->associates_db = $this->load->database('associates', TRUE);
    }

    public function getLeaveTypes()
    {
        // Use the associates database connection
        $query = $this->associates_db->select('*')->from('tblleavetype')->get();
        return $query->result_array(); // Return the results as an array
    }

    public function audit_trail($table)
    {
        // Append "_audit" to the table name
        $table = $table . "_audit";
        
        // Retrieve employee ID from session
        $employeeId = $this->session->userdata('employee_id');
        
        // Prepare the audit trail data
        $val = array(
            'empID'  => $employeeId,  // Using the updated employee ID
            'remarks' => NULL
        );
        
        // Select the maximum audit_id from the audit table
        $this->associates_db->select_max('audit_id');
        $query = $this->associates_db->get($table);
        $row = $query->row();
        
        // Define the WHERE clause based on the audit_id
        $where = array(
            'audit_id'  => $row->audit_id
        );
        
        // Update the audit trail with the new data
        $this->associates_db->update($table, $val, $where);
        
        // Return the audit_id of the updated row
        return $row->audit_id;
    }
    

    public function fileLeave($data)
    {
        // Insert the leave data into the tblleavefile table
        $inserted = $this->associates_db->insert('tblleavefile', $data); 
        
        // If the insert is successful, add an audit trail
        if ($inserted) {
            // Call the audit_trail function with the relevant table name (e.g., 'tblleavefile')
            $this->audit_trail('tblleavefile');
        }
    
        // Return the result of the insert operation
        return $inserted;
    }
    
    public function deleteLeave($filedNo)
{
    // Check if the leave exists before attempting deletion
    $this->associates_db->where('lvaFiledNo', $filedNo);
    if ($this->associates_db->count_all_results('tblleavefile') > 0) {
        // Proceed with deletion if leave exists
        $this->associates_db->where('lvaFiledNo', $filedNo);
        if ($this->associates_db->delete('tblleavefile')) {
            // If deletion is successful, log the action in the audit trail
            $this->audit_trail('tblleavefile');  // Add the audit trail

            return true;
        } else {
            // Log the error for debugging with the database error message
            log_message('error', 'Failed to delete leave with filed_no: ' . $filedNo . '. DB Error: ' . $this->associates_db->error()['message']);
            return false;
        }
    }

    // Log if leave is not found
    log_message('error', 'Leave not found for filed_no: ' . $filedNo);
    return false;
}

    public function approveLeave($filedNo, $approvedBy)
    {
        // Check if the leave exists and is currently pending before attempting update
        $this->associates_db->where('lvaFiledNo', $filedNo);
        $this->associates_db->where('lvaStatus', 'PENDING'); // Ensure only pending leaves are approved
        $leaveExists = $this->associates_db->count_all_results('tblleavefile') > 0;

        if ($leaveExists) {
            // Proceed with update if leave exists and is pending
            $this->associates_db->set('lvaStatus', 'APPROVED');  // Set status to approved
            $this->associates_db->set('lvaApprovedBy', $approvedBy); // Set the approved by information
            $this->associates_db->where('lvaFiledNo', $filedNo);
            if ($this->associates_db->update('tblleavefile')) {
                $this->audit_trail('tblleavefile'); // Log the approval action in the audit trail
                return true;
            } else {
                // Log the error for debugging with the database error message
                log_message('error', 'Failed to approve leave with filed_no: ' . $filedNo . '. DB Error: ' . $this->associates_db->error()['message']);
                return false;
            }
        }

        // Log if leave is not found or is not in pending status
        log_message('error', 'Leave not found or is not pending for filed_no: ' . $filedNo);
        return false;
    }

    public function disapproveLeave($filedNo, $comment)
    {
        // Check if the leave exists and is currently pending before attempting update
        $this->associates_db->where('lvaFiledNo', $filedNo);
        $this->associates_db->where('lvaStatus', 'PENDING');  // Ensure that only pending leaves are disapproved
        $leaveExists = $this->associates_db->count_all_results('tblleavefile') > 0;

        if ($leaveExists) {
            // Proceed with the update if leave exists and is pending
            $this->associates_db->set('lvaStatus', 'DISAPPROVED');  // Set status to disapproved
            $this->associates_db->set('lvaComments', $comment); // Set the comment
            $this->associates_db->where('lvaFiledNo', $filedNo);

            if ($this->associates_db->update('tblleavefile')) {
                $this->audit_trail('tblleavefile'); // Log the disapproval action in the audit trail
                return true;
            } else {
                // Log the error for debugging with the database error message
                log_message('error', 'Failed to disapprove leave with filed_no: ' . $filedNo . '. DB Error: ' . json_encode($this->associates_db->error()));
                return false;
            }
        }

        // Log if leave is not found or is not pending status
        log_message('error', 'Leave not found or is not pending for filed_no: ' . $filedNo);
        return false;
    }

    public function getPendingLeavesCount($employee_id)
    {
        //Query tblApprovingOfficer to get the corresponding empID
        $this->associates_db->select('empID');
        $this->associates_db->from('tblApprovingOfficer');
        $this->associates_db->where('empApprovingOfficer', $employee_id); // Match the current employee_id
        $query = $this->associates_db->get();

        if ($query->num_rows() > 0) {
            $approverEmpIDs = array_column($query->result_array(), 'empID'); // Get all empID values

            //Query tblleavefile for pending leaves with matching empID
            $this->associates_db->where_in('empID', $approverEmpIDs); // Filter by empID from tblApprovingOfficer
            $this->associates_db->where('lvaStatus', 'PENDING'); // Filter only pending leaves
            $this->associates_db->from('tblleavefile'); 

            return $this->associates_db->count_all_results();
        } else {
            // No matching empID found in tblApprovingOfficer
            return 0;
        }
    }

    public function getLeaveBalancesForEmployee($empID, $schoolYear)
    {
        $this->associates_db->select('empID, schoolyear, SL_Balance, VL_Balance, used_SL, used_VL');
        $this->associates_db->from('tblleavebalance');
        $this->associates_db->where('empID', $empID);
        $this->associates_db->where('schoolyear', $schoolYear);
        $query = $this->associates_db->get();
    
        // Log debug information
        log_message('debug', 'SQL Query: ' . $this->associates_db->last_query());
        log_message('debug', 'Query Result: ' . json_encode($query->row_array()));
    
        return $query->row_array();  // Return a single row or null
    }
    

    public function getSchoolYearByDate($date)
{
    // Query the database to find the school year that includes the given date
    $this->associates_db->select('start_date, end_date');
    $this->associates_db->from('tblleavebalance');
    $this->associates_db->where('start_date <=', $date);  // The start date must be before or equal to the given date
    $this->associates_db->where('end_date >=', $date);    // The end date must be after or equal to the given date
    $this->associates_db->limit(1);  // To make sure only one record is returned
    $query = $this->associates_db->get();

    return $query->row_array();  // This will return start_date and end_date
}

public function getApprovedLeavesForEmployee($employee_id, $startYear, $endYear)
{
    // Get the start_date and end_date for the given school year
    $this->associates_db->select('start_date, end_date');
    $this->associates_db->from('tblleavebalance');
    $this->associates_db->where('schoolyear', "{$startYear}{$endYear}");
    $query = $this->associates_db->get();
    $schoolYearData = $query->row_array();

    // Check if school year data exists
    if (!$schoolYearData) {
        return []; // No school year data found
    }

    $startDate = $schoolYearData['start_date'];
    $endDate = $schoolYearData['end_date'];

    // Get all approved leave records for the employee within the school year range
    $this->associates_db->select('lvaDays, lvaType, lvaReason, lvaDateFiled');
    $this->associates_db->from('tblleavefile');
    $this->associates_db->where('empID', $employee_id);
    $this->associates_db->where('lvaStatus', 'APPROVED');
    
    // Ensure the leave date is within the school year range (start_date and end_date)
    $this->associates_db->where('lvaDateFiled >=', $startDate);
    $this->associates_db->where('lvaDateFiled <=', $endDate);
    
    // Filter for only SL and VL (excluding VL with lvaReason 'cdld')
    $this->associates_db->group_start(); // Start of OR condition group
    $this->associates_db->where('lvaType', 'SL'); // SL type leaves
    $this->associates_db->or_where('lvaType', 'VL'); // VL type leaves
    $this->associates_db->group_end(); // End of OR condition group

    // Exclude VL records where lvaReason is 'cdld' (case insensitive)
    $this->associates_db->where('NOT (lvaType = "VL" AND LOWER(lvaReason) = "cdld")');

    $query = $this->associates_db->get();

    return $query->result_array();
}

public function countCdldVlLeavesForEmployee($employee_id, $startYear, $endYear)
{
    // Get all approved leave records for the employee within the school year range
    $this->associates_db->select('lvaDays, lvaReason');
    $this->associates_db->from('tblleavefile');
    $this->associates_db->where('empID', $employee_id);
    $this->associates_db->where('lvaStatus', 'APPROVED');
    
    // Ensure the leave date is within the school year range (start_date and end_date)
    $this->associates_db->where('lvaDateFiled >=', $startYear . '-01-01');
    $this->associates_db->where('lvaDateFiled <=', $endYear . '-12-31');
    
    // Filter for only VL leaves with the reason 'cdld'
    $this->associates_db->where('lvaType', 'VL');
    $this->associates_db->where('LOWER(lvaReason)', 'cdld');
    
    // Execute the query
    $query = $this->associates_db->get();

    $totalCdldDays = 0;

    // Loop through each leave record and sum up the days (lvaDays divided by 8 to convert hours to days)
    foreach ($query->result_array() as $leave) {
        // Convert lvaDays (in hours) to days (1 day = 8 hours)
        $totalCdldDays += $leave['lvaDays'] / 8;
    }

    return $totalCdldDays;  // Return the total number of CDLD leave days
}


public function updateLeaveBalance($employee_id, $schoolYearRange, $usedSL, $usedVL)
{
    // Log function entry and parameters
    log_message('debug', "Entering updateLeaveBalance with employee_id: {$employee_id}, schoolYearRange: {$schoolYearRange}, usedSL: {$usedSL}, usedVL: {$usedVL}");

    // Count the CDLD leaves for the employee within the given school year
    $cdldCount = $this->countCdldVlLeavesForEmployee($employee_id, substr($schoolYearRange, 0, 4), substr($schoolYearRange, 4, 4));

    // Log the CDLD leave count
    log_message('debug', "CDLD leave count for employee {$employee_id} in school year {$schoolYearRange}: {$cdldCount}");

    // Check if leave balance entry exists for this employee and school year
    $this->associates_db->where('empID', $employee_id);
    $this->associates_db->where('schoolyear', $schoolYearRange);
    $query = $this->associates_db->get('tblleavebalance');

    if ($query->num_rows() > 0) {
        // Update the used leave values and cdld_leave if the record exists
        $data = array(
            'used_VL' => $usedVL,
            'used_SL' => $usedSL,
            'cdld_leave' => $cdldCount
        );

        $this->associates_db->where('empID', $employee_id);
        $this->associates_db->where('schoolyear', $schoolYearRange);
        $this->associates_db->update('tblleavebalance', $data);

        // Log the update operation
        log_message('debug', "Updated leave balance for employee {$employee_id} in school year {$schoolYearRange}.");
    } else {
        // Insert a new record if it doesn't exist
        $data = array(
            'empID' => $employee_id,
            'schoolyear' => $schoolYearRange,
            'used_VL' => $usedVL,
            'used_SL' => $usedSL,
            'cdld_leave' => $cdldCount
        );

        $this->associates_db->insert('tblleavebalance', $data);

        // Log the insert operation
        log_message('debug', "Inserted new leave balance record for employee {$employee_id} in school year {$schoolYearRange}.");
    }
}

public function getuserDetails($employee_id)
{
    $this->associates_db->select('last_name, first_name, middle_name'); 
    $this->associates_db->where('employee_id', $employee_id);
    $query = $this->associates_db->get('associates');
    return $query->row_array(); 
}

    public function getAllLeaveApplications()
    {
        $this->associates_db->select('
            lvaFiledNo, 
            empID, 
            lvaDateFiled, 
            lvaDateFrom, 
            lvaDateTo, 
            lvaType, 
            lvaReason, 
            lvaStatus,
            lvaDays, 
            lvaSchoolYear, 
            lvaApprovedBy, 
            lvaComments, 
            lvaFiledType, 
            lvaStartTime, 
            lvaEndTime,
            lvaMedCert
        ');
        $this->associates_db->from('tblleavefile');
        $query = $this->associates_db->get();

        return $query->result_array(); 
    }
}
