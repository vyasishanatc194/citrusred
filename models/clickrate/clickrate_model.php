<?php
/*
	Model class for clickrate
*/
class Clickrate_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Clickrate_Model()
	{
		parent::__construct();
	}
	
	//function to create clickrate
	function create_clickrate($input_array)
	{
		$this->db->insert('red_click_rate',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update campaign
	function update_counter($input_array,$conditions_array)
	{
		$this->db->update('red_click_rate',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	
	//Function to get original url
	function get_encoded_url_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$result=$this->db->get_where('red_click_rate',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	
}
?>