<?php
class ConfigurationModel extends CI_Model
{	
	//Function to fetch configuration data
	function get_configuration_data($conditions_array=array())
	{ 
		$rows=array();
		$result=$this->db->get_where('red_configuration as rc',$conditions_array);
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch site configuration data
	function get_site_configuration_data($conditions_array=array())
	{
		$rows=array();
		$result=$this->db->get_where('red_site_configurations as rsc',$conditions_array);
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch site configuration data
	function get_site_configuration_data_as_array($conditions_array=array())
	{
		$rows=array();
		$result=$this->db->get_where('red_site_configurations as rsc',$conditions_array);
		
		foreach($result->result_array() as $row)
		{
			$arrKey = $row['config_name'];
			$arrVal = $row['config_value'];
			$rows[$arrKey]=$arrVal;
		}
		return $rows;
	}
	// Function to update configuration setting
	function update_site_configuration($input_array,$conditions_array)
	{
		$this->db->update('red_site_configurations',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	//Function to fetch email Personalize data
	function get_email_personalize_data($conditions_array=array())
	{
		$rows=array();
		$result=$this->db->get_where('red_email_personalization as rep',$conditions_array);
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	// Function to update email Personalize data
	function update_email_personalize($input_array,$conditions_array)
	{
		$this->db->update('red_email_personalization',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
}
?>