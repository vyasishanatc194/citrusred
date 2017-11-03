<?php
class Language_Model extends CI_Model
{	
	//Function to insert template header in database
	function create_language($input_array)
	{
		$this->db->insert('red_text_translation_languages',$input_array);
		return $this->db->insert_id();
	}	
	
	//Function to update template header in database
	function update_language($input_array,$conditions_array)
	{
		$this->db->update('red_text_translation_languages',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_delete' status of table to 1
	function delete_language($conditions_array)
	{
		$this->db->update('red_text_translation_languages',array('is_delete'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch template color data
	function get_language_data($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		$result=$this->db->get_where('red_text_translation_languages',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;			
		}
		return $rows;
	
	}	
	
	//Function to fetch template color count
	function get_language_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_text_translation_languages');
	}	
}
?>