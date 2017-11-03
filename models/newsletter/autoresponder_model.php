<?php
/*
	Model class for autoresponder
*/
class Autoresponder_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Autoresponder_Model(){
		parent::__construct();
	}
	
	//function to create autoresponder
	function create_autoresponder($input_array){
		$this->db->insert('red_email_autoresponders',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update autoresponder
	function update_autoresponder($input_array,$conditions_array){
		$this->db->update('red_email_autoresponders',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_autoresponder($conditions_array){
		//$this->db->delete('red_email_autoresponders',$conditions_array);
		$this->db->update('red_email_autoresponders',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch autoresponder data
	function get_autoresponder_data($conditions_array=array()){		
		$rows=array();
		
		$case=" (CASE
					WHEN autoresponder_scheduled_id >0 THEN (`autoresponder_scheduled_interval`	+ 0	)
					Else 0
					END) as conditional_order
					";
		$this->db->select("*, $case",false) ;			
		$this->db->order_by('conditional_order','asc');
		$this->db->order_by('campaign_id','desc');
		$result=$this->db->get_where('red_email_autoresponders',$conditions_array);
		 
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}
	
	//Fetch total count of campaigns
	function get_autoresponder_count($conditions_array=array()){
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_email_autoresponders');
	}
	
	//function to create scheduled campaign
	function create_scheduled_autoresponder($input_array){
		if(isset($input_array['is_verified'])){
			$is_verified = $input_array['is_verified'];
			$campaign_id = $input_array['autoresponder_id'];
			$this->db->query("update red_email_autoresponders set is_verified = '$is_verified' where campaign_id = '$campaign_id'");
		}
		unset($input_array['is_verified']);
		$this->db->replace_into('red_autoresponder_scheduled',$input_array);
		return $this->db->insert_id();
	
	}//function to update scheduled campaign
	function update_scheduled_autoresponder($input_array,$conditions_array){
		$this->db->update('red_autoresponder_scheduled',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	//Fetch total count of email templates
	function get_template_count($conditions_array=array()){
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_templates as rect');
		$this->db->where($conditions_array);
		$this->db->group_by('`rect`.`is_active`,`rect`.`is_delete`');
		#$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rect.template_id');
		$result=$this->db->get();
		

		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
	//Function to fetch template data
	function get_template_data($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();
		$this->db->select('distinct rect.*',false);
		$this->db->from('red_email_campaigns_templates as rect');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rect.template_id');
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Fetch total count of email templates according to autoresponder id
	function get_autoresponder_template_count($conditions_array=array()){
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_templates as rect');		
		$this->db->join('red_email_autoresponders as rea','rea.autoresponder_theme_id=rect.template_theme_id'); 
		$this->db->where($conditions_array);
		$this->db->group_by('rect.is_active');
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rect.template_id');

		$result=$this->db->get();		
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}		
		return $rows[0]['count'];
	}
	//Function to fetch template data according to autoresponder id
	function get_autoresponder_template_data($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();
		$this->db->select('distinct rect.*',false);
		$this->db->from('red_email_campaigns_templates as rect');
		$this->db->join('red_email_autoresponders as rea','rea.campaign_theme_id=rect.template_theme_id');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rect.template_id');
		$result=$this->db->get();	
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Fetch total count of email themes
	function get_theme_count($conditions_array=array()){
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_template_category as rect');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		$this->db->group_by(" red_is_active ,red_is_delete");
		$this->db->order_by('rect.red_theme_id');


		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
	//Function to fetch theme data
	function get_theme_data($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();
		$this->db->select('distinct rect.*',false);
		$this->db->from('red_email_campaigns_template_category as rect');		
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rect.red_theme_id');
		$result=$this->db->get();	
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch block skelton template data
	function get_template_blocks_data($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();
		$this->db->order_by('block_id');
		$result=$this->db->get_where('red_email_campaigns_template_blocks',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Function to fetch block names and content data
	function get_template_blocks_names_and_content_data($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();		
		$this->db->select('rectb.*,rectbc.*',false);
		$this->db->from('red_email_campaigns_template_blocks as rectb');		
		$this->db->join('red_email_campaigns_template_block_content as rectbc','rectb.block_name=rectbc.block_name');
		$this->db->where($conditions_array);		
		$this->db->limit($rows_per_page, $start);		
		$this->db->order_by('rectb.block_id');	
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}		
		return $rows;
	}
	
	//Function to fetch block names and content data count
	function get_template_blocks_names_and_content_count($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();		
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_template_blocks as rectb');		
		$this->db->join('red_email_campaigns_template_block_content as rectbc','rectb.block_name=rectbc.block_name');
		$this->db->group_by('page_id');		
		$this->db->where($conditions_array);		


		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}		
		return $rows[0]['count'];
	}
	//function to add block content
	function add_block_content($input_array){
		$this->db->insert('red_email_campaigns_template_block_content',$input_array);
		return $this->db->insert_id();
	}
	
	//Function to fetch template data
	function get_template_blocks_content_data($conditions_array=array()){
		$rows=array();
		$this->db->order_by('block_content_id');
		$result=$this->db->get_where('red_email_campaigns_template_block_content',$conditions_array);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//function to update block content
	function update_block_content($input_array,$conditions_array){
		$this->db->update('red_email_campaigns_template_block_content',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//function to create image bank campaign
	function create_image_bank($input_array){
		$this->db->insert('red_image_bank',$input_array);
		return $this->db->insert_id();
	}
	
	//Fetch total count of imagebank's images
	function get_image_bank_count($conditions_array=array()){
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_image_bank');
		$this->db->where($conditions_array);
		$this->db->group_by('img_is_delete,img_is_status');
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('img_id');

		$result=$this->db->get();			
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
	
	//Function to fetch imagebank's images data
	function get_image_bank_data($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();
		$this->db->select('distinct rib.*',false);
		$this->db->from('red_image_bank as rib');		
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rib.img_id');
		$result=$this->db->get();	
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch backgroundcolor blocks name
	function get_background_color_blocks_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('red_background_color_id');
		$result=$this->db->get_where('red_email_campaigns_background_color_blocks',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch backgroundcolor blocks content
	function get_background_color_blocks_content_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('red_background_color_block_content_id');
		$result=$this->db->get_where('red_email_campaigns_background_color_block_content',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//function to add background color block content
	function add_background_color_content($input_array)
	{
		$this->db->insert('red_email_campaigns_background_color_block_content',$input_array);
		return $this->db->insert_id();
	}
	//function to update background color block content
	function update_background_color_content($input_array,$conditions_array)
	{
		$this->db->update('red_email_campaigns_background_color_block_content',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	//Function to fetch background_color block names and content data count
	function get_background_color_blocks_names_and_content_count($conditions_array=array(),$rows_per_page=10,$start=0)

	{
		$rows=array();
		
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_background_color_blocks as rectb');
		
		$this->db->join('red_email_campaigns_background_color_block_content as rectbc','rectb.red_background_color_block_name 	=rectbc.red_background_color_block_name ');
		$this->db->where($conditions_array);
		$this->db->group_by('red_background_color_page_id');
		$result=$this->db->get();	
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
	//Function to fetch background color block names and content data
	function get_background_color_blocks_names_and_content_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		
		$this->db->select('rectb.*,rectbc.*',false);
		$this->db->from('red_email_campaigns_background_color_blocks as rectb');
		
		$this->db->join('red_email_campaigns_background_color_block_content as rectbc','rectb.red_background_color_block_name=rectbc.red_background_color_block_name');
		$this->db->where($conditions_array);
		
		$this->db->limit($rows_per_page, $start);
		
		$this->db->order_by('rectb.red_background_color_id');
	
		$result=$this->db->get();	
		
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}
	//Reset_color function, actually delete the value from red_email_campaigns_background_color_block_content table
	function reset_color($conditions_array)
	{
		$this->db->delete('red_email_campaigns_background_color_block_content',$conditions_array);
		return $this->db->affected_rows();
	}
	//Delete function, actually updating 'img_is_delete' status of table to 1
	function delete_image_bank($conditions_array)
	{
		//$this->db->delete('red_email_campaigns',$conditions_array);
		$this->db->update('red_image_bank',array('img_is_delete'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	//Function to fetch campaign data
	function get_shchedule_autoresponder_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('autoresponder_scheduled_id');
		$result=$this->db->get_where('red_autoresponder_scheduled',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch background_color block names and content data count
	function get_background_color_content_count($conditions_array=array())
	{

		$rows=array();		
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_background_color_block_content as rectb');
		$this->db->where($conditions_array);
		$this->db->group_by('red_background_color_page_id');

		$result=$this->db->get();	
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
	//Function to fetch background color block names and content data
	function get_background_color_content_data($conditions_array=array())
	{
		$rows=array();
		
		$this->db->select('rectb.*',false);
		$this->db->from('red_email_campaigns_background_color_block_content as rectb');
		$this->db->where($conditions_array);		
		$this->db->order_by('rectb.red_background_color_block_content_id');	
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Function to get abusive words from table
	function get_abusive_words($conditions_array=array()){
		$rows=array();
		
		$this->db->select('raw.*',false);
		$this->db->from('red_abusive_words as raw');
		$this->db->where($conditions_array);		
		$this->db->order_by('raw.id');	
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}
	//function to create autoresponder group
	function create_autoresponder_group($input_array){
		$this->db->insert('red_autoresponder_group',$input_array);
		return $this->db->insert_id();
	}
	//function to get autoresponder groups
	function get_autoresponder_group($conditions_array=array()){
		$rows=array();
		$this->db->from('red_autoresponder_group');
		$this->db->where($conditions_array);		
		$this->db->order_by('id');	
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}
	//function to update autoresponder group status
	function update_autoresponder_group($input_array,$conditions_array)
	{
		$this->db->update('red_autoresponder_group',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_autoresponder_group($conditions_array)
	{
		//$this->db->delete('red_email_autoresponders',$conditions_array);
		$this->db->update('red_email_autoresponders',array('is_deleted'=>1),array('autoresponder_group_id' => $conditions_array['id']));		
		$this->db->update('red_autoresponder_group',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	function get_theme_colors($conditions_array=array()){
		$rows=array();
		$member_array=array('-1',$this->session->userdata('member_id'));	//Collect memeber id
		$this->db->select('rect.*',false);
		$this->db->or_where_in('member_id', $member_array);
		$this->db->from('red_email_campaigns_color_themes as rect');
		$this->db->where($conditions_array);
		$this->db->order_by('rect.member_id');
		$this->db->order_by('rect.id');
	
		$result=$this->db->get();	
		
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}
}
?>