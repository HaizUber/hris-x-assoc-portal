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
        return $this->db->insert('tblleavefile', $data); // Make sure to use the correct table name
    }
}