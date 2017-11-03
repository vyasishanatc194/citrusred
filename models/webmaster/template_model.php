<?php
/*
	Model class for templates
*/
class Template_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Template_Model()
	{
		parent::__construct();
	}
	
	//function to create template
	function create_template($input_array)
	{
		$this->db->insert('red_email_templates',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update template
	function update_template($input_array,$conditions_array)
	{
		$this->db->update('red_email_templates',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_template($conditions_array)
	{
		//$this->db->delete('red_email_templates',$conditions_array);
		$this->db->update('red_email_templates',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch template data
	function get_template_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('template_id');
		$result=$this->db->get_where('red_email_templates',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch total count of templates
	function get_template_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_email_templates');
	}
}