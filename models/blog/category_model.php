<?php
/*
	Model class for campaign
*/
class Category_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Category_Model()
	{
		parent::__construct();
	}
	
	//function to create category
/* 	function create_category($input_array)
	{
		$this->db->insert('red_blog_tblcategory',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update category
	function update_category($input_array,$conditions_array)
	{
		$this->db->update('red_blog_tblcategory',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_category($conditions_array)
	{
		//$this->db->delete('red_blog_tblcategory',$conditions_array);
		$this->db->update('red_blog_tblcategory',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	} */
	
	//Function to fetch Category data
	function get_category_data($conditions_array=array())
	{
		$rows=array();
		$this->db->order_by('id');
		$result=$this->db->get_where('red_blog_tblcategory',$conditions_array);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	### Fetch Article Detail
	function get_article_detail($conditions_array=array())
	{
		$rows=array();

		$result=$this->db->get_where('red_blog_tblpost',$conditions_array);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
		//Function to fetch Category data
	function get_category_name($conditions_array=array())
	{
		$rows=array();
		$this->db->select('category_name');

		$query = $this->db->get('red_blog_tblcategory');

		$result=$this->db->get_where('red_blog_tblcategory',$conditions_array);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch total count of Category
	function get_category_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_blog_tblcategory');
	}
	
	### Fetch Count Arcived Month
	function get_archive_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_blog_tblpost');
	}
	
	//Function to fetch Arcived data
	function get_archive_data($conditions_array=array())
	{
		$rows=array();
		$this->db->order_by('added_on','desc');
		$result=$this->db->get_where('red_blog_tblpost',$conditions_array);
		
		foreach($result->result_array() as $row)
		{
		 
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch total count of Posts
	function get_post_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_blog_tblpost');
	}
	
	//Function to fetch Post data
	function get_post_data($conditions_array=array(),$rows_per_page,$start=0)
	{
		$rows=array();
		$this->db->order_by('id','desc');
		$result=$this->db->get_where('red_blog_tblpost',$conditions_array,$rows_per_page,$start);
		 
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		 
		return $rows;
	}
	
	function get_blog_setting_data()
	{
		$rows=array();
		$result=$this->db->get_where('red_blog_tblconfigurations');
		 
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
			define(strtoupper($row['config_name']), $row['config_value']);
			
		}
		 
		return $rows;
	
	}
	
	
	function get_comment_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_blog_tblcomment');
	}
	
	function get_comment_listing($conditions_array=array(),$rows_per_page,$start=0)
	{
		$rows=array();
		$this->db->order_by('id','desc');
		$result=$this->db->get_where('red_blog_tblcomment',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	
	}
	####	update comment
	function update_comment($input_array,$conditions_array)
	{
		$this->db->update('red_blog_tblcomment',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	##### create comment ############
	function create_comment($input_array)
	{
		$this->db->insert('red_blog_tblcomment',$input_array);
		return $this->db->insert_id();
	}
	
	##### create post ############
	function create_post($input_array)
	{
		$this->db->insert('red_blog_tblpost',$input_array);
		return $this->db->insert_id();
	}
	
	function create_image_listing($input_array)
	{
		$this->db->insert('red_blog_tblpost_images',$input_array);
		return $this->db->insert_id();
	
	}
	/**
		create reply
	**/
	function create_reply($input_array=array()){
		$this->db->insert('red_blog_tblreply',$input_array);
		return $this->db->insert_id();
	}
	/**
		Fetch Reply
	**/
	function get_reply_listing($conditions_array=array())
	{
		$rows=array();
		$this->db->order_by('id','desc');
		$result=$this->db->get_where('red_blog_tblreply',$conditions_array);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	
	}
	
	/**
		Function count_search_blog_result to count search products
	*/
	function count_search_product_result($conditions_array=array()){
		$this->db->select('count(id) as numrows');
		$this->db->from('red_blog_tblpost');
		$this->db->where($conditions_array);
		$this->db->like('title', $this->input->post('search_text'));
		$this->db->or_like('summary', $this->input->post('search_text'));
		$this->db->or_like('desc', $this->input->post('search_text'));
		$result=$this->db->get();		
		return $result->row()->numrows;		
	}	
	/**
		Function search_blog_result to fetch search products
	*/
	function search_product_result($conditions_array=array(),$rows_per_page=10,$start=0){
		$this->db->from('red_blog_tblpost');
		$this->db->where($conditions_array);
		if($this->input->post('search_text')){
			$search_text=$this->input->post('search_text');		
			$this->db->like('title', $search_text);
			$this->db->or_like('summary', $search_text); 
			$this->db->or_like('desc', $search_text); 
		}
		$this->db->limit($rows_per_page, $start);
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
}
?>