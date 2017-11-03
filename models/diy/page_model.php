<?php
/*
	Model class for page
*/
class Page_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Page_Model()
	{
		parent::__construct();
	}
	
	//function to create page
	function create_page($input_array)
	{
		$this->db->insert('red_diy_pages',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update page
	function update_page($input_array,$conditions_array)
	{
		$this->db->update('red_diy_pages',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_page($conditions_array)
	{
		//$this->db->delete('red_diy_pages',$conditions_array);
		$this->db->update('red_diy_pages',array('is_deleted'=>'Yes','page_position'=>0),$conditions_array);
		
		if($conditions_array['id']>0)
		{
			//$update_array=array('page_position'=>'page_position-1');
			//$conditions_array=array('id >'=>$conditions_array['id'],'is_deleted'=>'No');
			//$this->db->update('red_diy_pages',$update_array,$conditions_array);
			
			$sql='Update red_diy_pages SET page_position=page_position-1 Where id >'.$conditions_array['id']." and is_deleted='No'";
			
			$this->db->query($sql);
		}
		return $this->db->affected_rows();
	}
	
	//Function to fetch page data
	function get_page_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('page_position');
		$result=$this->db->get_where('red_diy_pages',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch total count of pages
	function get_page_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_diy_pages');
	}
	
	
	function getMax($column,$conditions_array=array())
	{
		//$this->db->where($conditions_array);
		
		$sql='Select max(`'.$column.'`) as getmax from red_diy_pages';
		if(count($conditions_array))
		{
			$conditions_str=implode(' and ',$conditions_array);
			$sql.=' Where '.$conditions_str;
		}
		
		$result=$this->db->query($sql);
		$row=$result->result_array();
		return $row;
	}
	
	//Function to fetch template data
	function get_template_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('id');
		$result=$this->db->get_where('red_diy_templates',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
}
?>