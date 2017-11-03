<?php
class TemplateHeader_Model extends CI_Model
{	
	//Function to insert template header in database
	function create_header($input_array)
	{
		$this->db->insert('red_email_campaigns_templates',$input_array);
		return $this->db->insert_id();
	}	
	
	//Function to update template header in database
	function update_header($input_array,$conditions_array)
	{
		$this->db->update('red_email_campaigns_templates',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_delete' status of table to 1
	function delete_header($conditions_array)
	{
		$this->db->update('red_email_campaigns_templates',array('is_delete'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch template header data
	function get_header_data($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		//$this->db->join('red_email_campaigns_template_category','red_theme_id=template_theme_id');
		$result=$this->db->get_where('red_email_campaigns_templates',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;			
		}
		return $rows;
	
	}	
	
	//Function to fetch template header count
	function get_header_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_email_campaigns_templates');
	}	
}
?>