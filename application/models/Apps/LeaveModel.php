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
        // Insert leave data into the correct table using the associates database
        return $this->associates_db->insert('tblleavefile', $data); 
    }

    public function deleteLeave($filedNo)
    {
        // Check if the leave exists before attempting deletion (optimized query)
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
            lvaSchoolYear, 
            lvaApprovedBy, 
            lvaComments, 
            lvaFiledType, 
            lvaStartTime, 
            lvaEndTime
        ');
        $this->associates_db->from('tblleavefile');
        $query = $this->associates_db->get();

        return $query->result_array(); // Returns an associative array of data
    }
}
