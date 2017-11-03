<?php
class Pmta_log_model extends CI_Model {
 
	private $tbl_pmtalog= 'red_pmtalog';
 
	function pmta_log_model(){
		parent::__construct();
	}
 
	function count_all(){
		
		return $this->db->count_all($this->tbl_pmtalog);
	}
 
	function count_all_search($sql){		
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	function count_all_records($sql){		
		$query = $this->db->query($sql);
		$arrCounter = $query->result_array();
		return $arrCounter[0]['totRec'];
	}
 
	function get_paged_list($limit = 10, $offset = 0){
		$this->db->order_by('logid','asc');
		return $this->db->get($this->tbl_pmtalog, $limit, $offset);
	}
 
	function get_search_pagedlist($sql,$limit = 10, $offset = 0){
		$sql = $sql . " LIMIT $offset, $limit"; 
		return $this->db->query($sql, false);
	}
 
}
?>