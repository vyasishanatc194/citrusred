<?php
class BlogCategory_Model extends CI_Model
{
	
	//Function to insert listing category in database
	function create_category($input_array)
	{
		$this->db->insert('red_blog_tblcategory',$input_array);
		return $this->db->insert_id();
	}
	
	
	//Function to update listing category in database
	function update_category($input_array,$conditions_array)
	{
		$this->db->update('red_blog_tblcategory',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'id' status of table to 1
	function delete_category($conditions_array)
	{
		$this->db->delete('red_blog_tblcategory',$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch listing category data
	function get_category_data($conditions_array,$rows_per_page=10,$start=0)
	{
	
		$rows=array();
		$result=$this->db->get_where('red_blog_tblcategory',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
			
		}
		
		return $rows;
	
	}
	//Function to fetch parent listing category data

	//Function to fetch subcategory listing category data
	function get_sub_category_data($conditions_array)
	{
		$rows=array();
		$result=$this->db->get_where('red_blog_tblcategory',$conditions_array);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	
	}
	
	//Function to fetch Listing Category count
	function get_category_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_blog_tblcategory');
	}	
	//Function to insert local business listing  in database
	function create_listing($input_array)
	{
		$this->db->insert('red_blog_tblcategory',$input_array);
		return $this->db->insert_id();
	}
	
	//Function to fetch local business listing  count
	function get_listing_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_blog_tblcategory');
	}	
	//Function to fetch local business listing  data
	function get_listing_data($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		$result=$this->db->get_where('red_blog_tblcategory',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
			
		}
		return $rows;
	
	}
	//Function to update listing  in database
	function update_listing($input_array,$conditions_array)
	{
		$this->db->update('red_blog_tblcategory',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_listing($conditions_array)
	{
		$this->db->update('red_blog_tblcategory',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
}
?>