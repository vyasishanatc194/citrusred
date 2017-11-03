<?php
class ListingCategory_Model extends CI_Model
{
	
	//Function to insert listing category in database
	function create_category($input_array)
	{
		$this->db->insert('red_listing_category',$input_array);
		return $this->db->insert_id();
	}
	
	
	//Function to update listing category in database
	function update_category($input_array,$conditions_array)
	{
		$this->db->update('red_listing_category',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'listing_category_delete' status of table to 1
	function delete_category($conditions_array)
	{
		$this->db->update('red_listing_category',array('listing_category_delete'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch listing category data
	function get_category_data($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		$result=$this->db->get_where('red_listing_category',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
			
		}
		return $rows;
	
	}
	//Function to fetch parent listing category data
	function get_parent_category_data($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		$result=$this->db->get_where('red_listing_category',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			
			/*************************** Fetch subcategory from database *******************************/
			$conditions_subcategory_array=array('listing_category_parent'=>$row['listing_category_id'],'listing_category_delete'=>0);
			$subcategory_rows=$this->get_sub_category_data($conditions_subcategory_array);
			$count_subcategory=count($subcategory_rows);
			//If parent category have subcategory than $row['delete']=0 otherewise it woll contain 1 value
			if($count_subcategory>0){
				$row['delete']=0;
			}else{
				$row['delete']=1;
			}
			$rows[]=$row;
			foreach($subcategory_rows AS $subcategory_row){
				$subcategory_row['delete']=1;
				$rows[]=$subcategory_row;
			}
			/**********************************************************************/
		}
		return $rows;
	
	}
	//Function to fetch subcategory listing category data
	function get_sub_category_data($conditions_array)
	{
		$rows=array();
		$result=$this->db->get_where('red_listing_category',$conditions_array);
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
		return $this->db->count_all_results('red_listing_category');
	}	
	//Function to insert local business listing  in database
	function create_listing($input_array)
	{
		$this->db->insert('red_listing',$input_array);
		return $this->db->insert_id();
	}
	
	//Function to fetch local business listing  count
	function get_listing_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_listing');
	}	
	//Function to fetch local business listing  data
	function get_listing_data($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		$result=$this->db->get_where('red_listing',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
			
		}
		return $rows;
	
	}
	//Function to update listing  in database
	function update_listing($input_array,$conditions_array)
	{
		$this->db->update('red_listing',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_listing($conditions_array)
	{
		$this->db->update('red_listing',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
}
?>