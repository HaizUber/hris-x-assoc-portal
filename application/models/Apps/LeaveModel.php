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

    public function fileLeave($data)
    {
        return $this->associates_db->insert('tblleavefile', $data); 
    }

    public function deleteLeave($filedNo)
    {
        // Check if the leave exists before attempting deletion
        $this->associates_db->where('lvaFiledNo', $filedNo);
        if ($this->associates_db->count_all_results('tblleavefile') > 0) {
            // Proceed with deletion if leave exists
            $this->associates_db->where('lvaFiledNo', $filedNo);
            if ($this->associates_db->delete('tblleavefile')) {
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

    public function getLeaveBalances($employee_id)
    {
        // Query to fetch leave balance information for the given employee
        $this->associates_db->select('empID, sickLeaveBalance, vacationLeaveBalance, totalSickLeaveUsed, totalVacationLeaveUsed');
        $this->associates_db->from('tblleavebalance');
        $this->associates_db->where('empID', $employee_id); 
        $query = $this->associates_db->get();

        return $query->num_rows() > 0 ? $query->row_array() : null;
    }

    public function getLeaveDetails($filedNo)
    {
        $this->associates_db->select('lvaDateTo, lvaDateFrom, empID, lvaType'); 
        $this->associates_db->where('lvaFiledNo', $filedNo);
        $query = $this->associates_db->get('tblleavefile');
        return $query->row_array(); 
    }

    public function updateLeaveBalance($employeeId, $leaveType, $duration)
    {
        // Check if the employee ID exists in tblleavebalance before updating
        $this->associates_db->select('empID');
        $this->associates_db->from('tblleavebalance');
        $this->associates_db->where('empID', $employeeId);
        $query = $this->associates_db->get();
    
        if ($query->num_rows() == 0) {
            log_message('error', 'Employee ID ' . $employeeId . ' not found in tblleavebalance.');
            return false; // Employee not found
        }
    
        // Construct the SQL query based on the leave type
        if ($leaveType == 'VL') {
            $sql = "UPDATE tblleavebalance 
                    SET totalVacationLeaveUsed = totalVacationLeaveUsed + ? 
                    WHERE empID = ?";
        } elseif ($leaveType == 'SL') {
            $sql = "UPDATE tblleavebalance 
                    SET totalSickLeaveUsed = totalSickLeaveUsed + ? 
                    WHERE empID = ?";
        } else {
            log_message('error', 'Invalid leave type: ' . $leaveType);
            return false; // Invalid leave type
        }
    
        // Run the query with the parameters
        $this->associates_db->query($sql, array((int)$duration, $employeeId));
    
        // Check if the update was successful
        if ($this->associates_db->affected_rows() > 0) {
            return true; // Update successful
        } else {
            log_message('error', 'Failed to update leave balance for employee ID: ' . $employeeId);
            return false; // Update failed
        }
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
