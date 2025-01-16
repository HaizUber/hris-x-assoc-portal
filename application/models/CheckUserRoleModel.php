<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CheckUserRoleModel extends CI_Model
{
    private $associates_db;
    public function __construct()
    {
        parent::__construct();
        $this->associates_db = $this->load->database('associates', TRUE);
    }

    // Method to check if the employee is an Approving Officer
    public function check_approving_officer($employee_id)
    {
        // Query the tblapprovingofficer table
        $this->associates_db->where('empApprovingOfficer', $employee_id);
        $query = $this->associates_db->get('tblapprovingofficer');
    
        // Log the query and result count
        log_message('debug', 'Checking if employee is an approving officer. Employee ID: ' . $employee_id . ' | Found: ' . $query->num_rows());
    
        // Return true if employee_id exists, otherwise false
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
     // Method to check if the employee is in HR
     public function check_hr($employee_id)
     {
         // Query the associates_dept table
         $this->associates_db->where('employee_id', $employee_id);
         $this->associates_db->where('department', 'HRO');
         $query = $this->associates_db->get('associates_dept');
     
         // Log the query and result count
         log_message('debug', 'Checking if employee is in HR. Employee ID: ' . $employee_id . ' | Found: ' . $query->num_rows());
     
         // Return true if department is HRO, otherwise false
         return $query->num_rows() > 0;
     }
 }    