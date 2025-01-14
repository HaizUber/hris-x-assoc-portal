<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LeaveModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        // Load the database library if not already autoloaded
        $this->load->database();
    }

    public function getLeaveTypes()
    {
        // Query the database to fetch leave types from the correct table
        $query = $this->db->select('*')->from('tblleavetype')->get();

        return $query->result_array(); // Return the results as an array
    }

    public function fileLeave($data)
    {
        // Insert leave data into the correct table
        return $this->db->insert('tblleavefile', $data); 
    }

    public function getAllLeaveApplications() {
        $this->db->select('
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
        $this->db->from('tblleavefile');
        $query = $this->db->get();

        return $query->result_array(); // Returns an associative array of data
    }
}