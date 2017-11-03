<?php

class Croncb_Model extends CI_Model {

    function Croncb_Model() {
        parent::__construct();
    }

    function get_count_cron_data($conditions_array = array()) {
        $rows = array();

        $this->db->select('COUNT(*) as totalcount,rc_member_id,rc_id', 'cp.rc_member_id=rm.subscriber_created_byIndex');
		//$this->db->join('red_email_subscribers as rm', 'cp.rc_member_id=rm.subscriber_created_by and subscriber_id > last_subscriber_id');
        $this->db->join('red_email_subscribers as rm', 'cp.rc_member_id=rm.subscriber_created_by');
        $this->db->group_by('rc_member_id,rc_id');

        $result = $this->db->get_where('red_dv_cron_setup as cp', $conditions_array);

		##echo $this->db->last_query();exit;
        foreach ($result->result_array() as $row) {
            $rows[$row['rc_member_id']]['totalcount'] = $row['totalcount'];
            $rows[$row['rc_member_id']]['member_id'] = $row['rc_member_id'];
            $rows[$row['rc_member_id']]['rc_id'] = $row['rc_id'];
            #$rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    function get_cron_all_data($conditions_array = array()) {
        $rows = array();

        $this->db->select('*', 'cp.rc_member_id=rm.subscriber_created_byIndex');
        $this->db->join('red_email_subscribers as rm', 'cp.rc_member_id=rm.subscriber_created_by');
        $result = $this->db->get_where('red_dv_cron_setup as cp', $conditions_array);

        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }

        $result->free_result();
        return $rows;
    }

    function get_cron_data($conditions_array = array(), $offset, $itempage) {
        $rows = array();

        $this->db->select('*', 'cp.rc_member_id=rm.subscriber_created_by');
        $this->db->join('red_email_subscribers as rm', 'cp.rc_member_id=rm.subscriber_created_by');
		$this->db->group_by('subscriber_id'); 
        $result = $this->db->get_where('red_dv_cron_setup as cp', $conditions_array, $itempage, $offset);

        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    function selectCron($conditions_array) {
        $this->db->select('*');
		$this->db->order_by("rc_id", "desc");
        $result = $this->db->get_where('red_dv_cron_setup as cp', $conditions_array);
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }





    function selectSingleCsvCron($conditions_array) {
        $this->db->select('*', 'cp.rc_id=dc.dv_rc_id');
        $this->db->join('red_dv_csv as dc', 'cp.rc_id=dc.dv_rc_id');
        $result = $this->db->get_where('red_dv_cron_setup as cp', $conditions_array);
        
        
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    
    
    
    

    function insertCron($input_array) {
        $this->db->insert('red_dv_cron_setup', $input_array);
        return $this->db->affected_rows();
    }

    function updateCron($input_array, $conditions_array) {
        $this->db->update('red_dv_cron_setup', $input_array, $conditions_array);
        return $this->db->affected_rows();
    }

    function update_account($input_array, $conditions_array) {
        $this->db->update('red_email_subscribers', $input_array, $conditions_array);
        return $this->db->affected_rows();
    }

    function insertErrorLog($input_array) {
        $this->db->insert('red_dv_errorlog', $input_array);
        return $this->db->affected_rows();
    }

    function insertCsvLog($input_array) {
        $this->db->insert('red_dv_csv', $input_array);
		
        return $this->db->affected_rows();
    }

    function update_CsvLog($input_array, $conditions_array) {
        $this->db->update('red_dv_csv', $input_array, $conditions_array);
        return $this->db->affected_rows();
    }

    function selectCsvLog($conditions_array) {
        $this->db->select('*');
        $result = $this->db->get_where('red_dv_csv as cp', $conditions_array);
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

}

?>