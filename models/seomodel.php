<?php
class SeoModel extends CI_Model
{	
	//Constructor class with parent constructor
	function Campaign_Model()
	{
		parent::__construct();
	}
	function create_seo($input_array=array())
	{
		$this->db->insert('red_seo',$input_array);
		return $this->db->insert_id();
	}
	
	function update_seo($input_array,$conditions_array)
	{	
		$this->db->update('red_seo',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}	
	
	function get_page($pageid){
		$rows=array();
		$this->db->from('red_seo');
		$this->db->where(array('id'=>$pageid));
		$result=$this->db->get();
		 
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;		
	}
	function get_seo_data($conditions_array=array(),$like=false,$like_value="",$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->from('red_seo');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		if($like){
			if($like_value=="blog"){
				$current_url_like= $like_value;
			}else{
				$current_url_like= $_SERVER['REQUEST_URI'];
			}
			
			$arr_current_url_like = explode('/',$current_url_like); 
			#$current_url_like = str_replace(base_url(),'www.'.SYSTEM_DOMAIN_NAME.'/','http://'.$_SERVER["HTTP_HOST"].$current_url_like);
			$current_url_like = str_replace(base_url(),'www.'.SYSTEM_DOMAIN_NAME.'/',$arr_current_url_like[count($arr_current_url_like)-1]);
			
			$this->db->like('page',$current_url_like);
		}
		$result=$this->db->get();
		#echo $this->db->last_query();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	
	}
	function get_seo_data_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_seo');
	}	
}
?>