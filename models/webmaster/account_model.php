<?php 
class Account_Model extends CI_Model
{
	function Account_Model()
	{
		parent::__construct();
	}
	
	function get_account_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		
		$result=$this->db->get_where('red_webmaster_account',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	function update_account($input_array,$conditions_array)
	{
		$this->db->update('red_webmaster_account',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
}
?>