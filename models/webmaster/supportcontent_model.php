<?php
class SupportContent_Model extends CI_Model
{	
	//Function to insert support content in database
	function create_product($input_array)
	{
		$this->db->insert('red_support_product',$input_array);
		return $this->db->insert_id();
	}	
	
	//Function to update template header in database
	function update_product($input_array,$conditions_array)
	{
		$this->db->update('red_support_product',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_delete' status of table to 1
	function delete_product($conditions_array)
	{
		$this->db->update('red_support_product',array('is_delete'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch template header data
	function get_product_data($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->select('rsp.id,category_id, product,description,rsp.is_active,category');
		$this->db->join('red_support_category AS rsc','rsp.category_id=rsc.id','left');
		$result=$this->db->get_where('red_support_product AS rsp',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;			
		}
		return $rows;
	
	}	
	
	//Function to fetch template header count
	function get_product_count($conditions_array=array())
	{
		$this->db->join('red_support_category AS rsc','rsc.id=rsp.category_id');
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_support_product AS rsp');
	}	
}
?>