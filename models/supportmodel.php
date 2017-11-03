<?php
class SupportModel extends CI_Model
{	
	/**
		Function get_category_data to fetch categories from database
	*/
	function get_category_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->from('red_support_category as rsc');
		$this->db->where($conditions_array);
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	
	}
	/**
		Function get_category_productdata to fetch products from database
	*/
	function get_category_productdata($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->from('red_support_category as rsc');
		$this->db->join('red_support_product as rsp','rsc.id=rsp.category_id');
		$this->db->where($conditions_array);
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	
	}
	/**
		Function search_product_result to fetch search products
	*/
	function search_product_result($conditions_array=array(),$rows_per_page=10,$start=0){
		$this->db->from('red_support_product as rsp');
		$this->db->where($conditions_array);
		if($this->input->post('search_text')){
			$search_text=$this->input->post('search_text');
		}else{
			$search_text="";
		}
		$this->db->where("(product like'%$search_text%' or description  like'%$search_text%')");
		$this->db->limit($rows_per_page, $start);
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	/**
		Function count_search_product_result to count search products
	*/
	function count_search_product_result($conditions_array=array()){
		$this->db->where($conditions_array);
		$this->db->like('product', $this->input->post('search_text'));
		$this->db->or_like('description', $this->input->post('search_text'));
		return $this->db->count_all_results('red_support_product as rsp');
	}	
}
?>