<?php
class Feedback_Model extends CI_Model
{	
	//Constructor class with parent constructor
	function Feedback_Model()
	{
		parent::__construct();
	}
	
	//function to create feeback
	function create_feedback($input_array)
	{
		$this->db->insert('red_feedback',$input_array);
		return $this->db->insert_id();
	}
	// Get feedback list
	function get_feedback_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->from('red_feedback as rf');
		$this->db->where($conditions_array);
		$this->db->order_by("id", "desc");
		$this->db->limit($rows_per_page,$start);
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	
	}
	//Fetch total count of fedback
	function get_feedback_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_feedback');
	}
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_feedback($conditions_array)
	{
		$this->db->update('red_feedback',array('is_delete'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
}
?>