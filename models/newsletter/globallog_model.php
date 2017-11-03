<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Globallog_Model extends CI_Model {
     function addlog($input_array) {
        # added for the subscription_id for All My Contact in DB_Tbl red_email_subscribers
        
        $this->db->insert('red_global_field_job', $input_array);
        return $this->db->insert_id();
    }
    
    function update_log($input_array, $conditions_array) {
      
        $this->db->update('red_global_field_job', $input_array, $conditions_array);
        //  echo $this->db->last_query();exit;
        return $this->db->affected_rows();
    }
	
	function get_log_data($mid){
		
		$result = $this->db->query("Select * from red_global_field_job where member_id='$mid'");
		
		
		  if ($result->num_rows() > 0) {
			  foreach ($result->result_array() as $row => $val) {
				
				  $rows[$val['member_id']]['id']=$val['id'];
				  $rows[$val['member_id']]['job_status']=$val['job_status'];
			  }
		  }
		 //echo '<pre>'; print_r($rows);exit;
		  return $rows;
		
	}
	
	 function getlog($conditions_array) {
      
        $this->db->from('red_global_field_job');
		  $this->db->where($conditions_array);
        //  echo $this->db->last_query();exit;
        $result = $this->db->get();
		foreach ($result->result_array() as $row) {
            $rows = $row;
        }
        $result->free_result();
		//echo '<pre>';print_r($rows);exit;
        return $rows;
    }
}

