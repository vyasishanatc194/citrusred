<?php
/**
  *	Model class for pmta 
  *	It have  functions for interaction with database.
 */
class Pmta_Model extends CI_Model
{
	//Constructor functon for pmta with parent constructor
	function Pmta_Model()
	{
		parent::__construct();
	}
	
	/**
	 *	Function create_pmta
	 *
	 *	Function to create new pmta
	 *
	 *	@param (array) (input_array)  values to insert into database
	 *
	 *	@return (int) return inserted pmta id
	 */
	function create_pmta($input_array)
	{
		$this->db->insert('red_pmtalog',$input_array);
		return $this->db->insert_id();
	}
	function update_pmta($input_array,$conditions_array)
	{
		$this->db->update('red_pmtalog',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	//Function to fetch configuration data
	function get_pmta_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('logid','desc');
		$result=$this->db->get_where('red_pmtalog as rp',$conditions_array,$rows_per_page,$start);
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
}
?>