<?php
/*
	Model class for website
*/
class Website_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Website_Model()
	{
		parent::__construct();
	}
	
	//function to create website
	function create_website($input_array)
	{
		$this->db->insert('red_diy_websites',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update website
	function update_website($input_array,$conditions_array)
	{
		$this->db->update('red_diy_websites',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_website($conditions_array)
	{
		//$this->db->delete('red_diy_websites',$conditions_array);
		$this->db->update('red_diy_websites',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch website data
	function get_website_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('website_id');
		$result=$this->db->get_where('red_diy_websites',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch total count of websites
	function get_website_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_diy_websites');
	}
	
	//Function to fetch template data
	function get_template_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		
		
		
		$this->db->select('distinct rdt.*,rdtc.*',false);
		$this->db->from('red_diy_templates as rdt');
		
		$this->db->join('red_diy_template_categories as rdtc','rdt.template_category_id=rdtc.category_id');
		
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rdt.template_id');
		$result=$this->db->get();	
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch total count of templates
	function get_template_count($conditions_array=array())
	{
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_diy_templates as rdt');
		
		$this->db->join('red_diy_template_categories as rdtc','rdt.template_category_id=rdtc.category_id');
		
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rdt.template_id');
		$result=$this->db->get();	
		
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
	
	//Function to fetch block skelton template data
	function get_template_blocks_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('block_id');
		$result=$this->db->get_where('red_diy_template_blocks',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Function to fetch template data
	function get_template_blocks_content_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('block_content_id');
		$result=$this->db->get_where('red_diy_template_block_content',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//function to add block content
	function add_block_content($input_array)
	{
		$this->db->insert('red_diy_template_block_content',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update block content
	function update_block_content($input_array,$conditions_array)
	{
		$this->db->update('red_diy_template_block_content',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//function to add block data
	function add_block_data($input_array)
	{
		$this->db->insert('red_diy_template_blocks',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update block data
	function update_block_data($input_array,$conditions_array)
	{
		$this->db->update('red_diy_template_blocks',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch block names and content data
	function get_template_blocks_names_and_content_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		
		$this->db->select('rdtb.*,rdtbc.*',false);
		$this->db->from('red_diy_template_blocks as rdtb');
		
		$this->db->join('red_diy_template_block_content as rdtbc','rdtb.block_name=rdtbc.block_name');
		$this->db->where($conditions_array);
		
		$this->db->limit($rows_per_page, $start);
		
		$this->db->order_by('rdtb.block_id');
	
		$result=$this->db->get();	
		
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}
	
	//Function to fetch block names and content data count
	function get_template_blocks_names_and_content_count($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_diy_template_blocks as rdtb');
		
		$this->db->join('red_diy_template_block_content as rdtbc','rdtb.block_name=rdtbc.block_name');
		$this->db->where($conditions_array);
		
		$result=$this->db->get();	
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
}
?>