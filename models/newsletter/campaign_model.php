<?php
/*
	Model class for campaign
*/
class Campaign_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Campaign_Model()
	{
		parent::__construct();
	}
	
	//function to create campaign
	function create_campaign($input_array)
	{
		$this->db->insert('red_email_campaigns',$input_array);
		return $this->db->insert_id();
	}
	
	//function to update campaign
	function update_campaign($input_array,$conditions_array){
		$this->db->update('red_email_campaigns',$input_array,$conditions_array);
		# echo $this->db->last_query();
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_campaign($conditions_array){		
		$this->db->update('red_email_campaigns',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	function delete($conditions_array){		
		$this->db->delete('red_email_campaigns',$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch campaign data
	function get_campaign_data($conditions_array=array(),$rows_per_page=10,$start=0,$order_by="desc",$order_field="campaign_id",$join=false){
		$rows=array();
		
		if($order_field=="case"){
			$case=" (CASE
						WHEN `campaign_status`='draft' THEN `campaign_date_added` 
						WHEN `campaign_status`='active' THEN `email_send_date` 
						WHEN `campaign_status`='archived' or `campaign_status`='active_ready' or `campaign_status`='ready' or `campaign_status`='disallow'  THEN `campaign_sheduled`
						END) as conditionalorder
						";
			if($join){
				$this->db->select("rec.*,rm.member_username,rm.member_id, $case",false) ;
			}else{
				$this->db->select("*, $case",false) ;
			}			 
			$this->db->order_by('conditionalorder','desc');
			$this->db->order_by('campaign_id','desc');
		}else if($order_field!=""){
			if($join){
				$this->db->select("rec.*,rm.member_username,rm.member_id",false) ;
			}else{
				$this->db->select("*",false) ;
			}
			$this->db->order_by('rec.campaign_priority','desc');
			$this->db->order_by('rec.is_segmentation','desc');
			$this->db->order_by($order_field,$order_by);
		}else{
			if($join){
				$this->db->select("rec.*,rm.member_username,rm.member_id",false) ;
			}else{
				$this->db->select("*",false) ;
			}
			$this->db->order_by('campaign_status',$order_by);
		}
		if($join)
		$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');
		$result=$this->db->get_where('red_email_campaigns as rec',$conditions_array,$rows_per_page,$start);
		
		foreach($result->result_array() as $row){
			$rows[]=$row;
		}		
		return $rows;
	}
	
	//Fetch total count of campaigns
	function get_campaign_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		#echo $this->db->_compile_select(); 
		#echo $this->db->last_query();
		return $this->db->count_all_results('red_email_campaigns as rec');
	}
	
	//function to create scheduled campaign
	function create_scheduled_campaign($input_array)
	{
		$this->db->insert('red_email_campaigns_scheduled',$input_array);
		return $this->db->insert_id();
	}
	//function to update scheduled campaign
	function update_scheduled_campaign($input_array,$conditions_array)
	{
		$this->db->update('red_email_campaigns_scheduled',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	 
 //Fetch total count of email templates
	function get_template_count($conditions_array=array())
	{
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_templates as rect');
		$this->db->where($conditions_array);
		$this->db->order_by('rect.template_name');
		$this->db->group_by('`rect`.`is_active`,`rect`.`is_delete`');
		$result=$this->db->get();		
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
	
	//Function to fetch template data
	function get_template_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->select('distinct rect.*',false);
		$this->db->from('red_email_campaigns_templates as rect');	
		
		/* $this->db->join('red_email_campaigns as rec','rec.campaign_theme_id=rect.template_theme_id'); */	
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page, $start);
		$this->db->order_by('rect.template_name desc');
		$result=$this->db->get();	
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}	
	//Fetch total count of email templates according to campaign id
	function get_campaign_template_count($conditions_array=array())
	{
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_templates as rect');
		
		$this->db->join('red_email_campaigns as rec','rec.campaign_theme_id=rect.template_theme_id'); 
		$this->db->where($conditions_array);
		$this->db->group_by('rect.is_active');
		$this->db->order_by('rect.template_id');
		$result=$this->db->get();		
		# print_r($conditions_array);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['count'];
	}
	//Function to fetch template data according to campaign id
	function get_campaign_template_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->select('distinct rect.*',false);
		$this->db->from('red_email_campaigns_templates as rect');		
		$this->db->join('red_email_campaigns as rec','rec.campaign_theme_id=rect.template_theme_id');
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
	function get_theme_count($conditions_array=array())
	{
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_email_campaigns_template_category as rect');
		$this->db->where($conditions_array);
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
	function get_theme_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
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
	function get_template_blocks_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('block_id');
		$result=$this->db->get_where('red_email_campaigns_template_blocks',$conditions_array);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Function to fetch block names and content data
	function get_template_blocks_names_and_content_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
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
	function get_template_blocks_names_and_content_count($conditions_array=array(),$rows_per_page=10,$start=0)
	{
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
	function add_block_content($input_array)
	{
		$this->db->insert('red_email_campaigns_template_block_content',$input_array);
		return $this->db->insert_id();
	}
	
	//Function to fetch template data
	function get_template_blocks_content_data($conditions_array=array())
	{
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
	function update_block_content($input_array,$conditions_array)
	{
		$this->db->update('red_email_campaigns_template_block_content',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//function to create image bank campaign
	function create_image_bank($input_array)
	{
		$this->db->insert('red_image_bank',$input_array);
		return $this->db->insert_id();
	}
	
	//Fetch total count of imagebank's images
	function get_image_bank_count($conditions_array=array()){
		$this->db->select('distinct count(*) as count',false);
		$this->db->from('red_image_bank');
		$this->db->where($conditions_array);
		$this->db->group_by('img_is_delete,img_is_status');
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
		$this->db->group_by('red_background_color_page_id');
		$this->db->where($conditions_array);
		
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
		$this->db->update('red_image_bank',array('img_is_delete'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	//Delete function, actually updating 'is_delete' status of table to 1
	function delete_theme_color($conditions_array)
	{
		//$this->db->delete('red_email_campaigns',$conditions_array);
		$this->db->update('red_email_campaigns_color_themes',array('is_delete'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	//Function to fetch campaign data
	function get_shchedule_campaign_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		
		
  		$case=" (CASE
					WHEN campaign_status='draft' THEN `campaign_date_added` 
					WHEN campaign_status='active' THEN `email_send_date`
					WHEN campaign_status='archived' or campaign_status='queueing' or campaign_status='active_ready' or campaign_status='ready' or campaign_status='disallow'  THEN `campaign_sheduled`
					END) as conditionalorder
					";  
		$this->db->select("rec.*,$case",false) ;	
		
		$this->db->order_by("conditionalorder",'desc');
		$this->db->order_by('rec.`campaign_id`','desc');
		
		#echo $this->db->_compile_select(); 
		//$this->db->join('red_email_campaigns_scheduled as rs','rs.campaign_id=rec.campaign_id','left');
		$result=$this->db->get_where('red_email_campaigns as rec',$conditions_array,$rows_per_page,$start);
		#echo $this->db->last_query(); 
		 
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}
	// Start Code by cb for getting id for campaign with ab testing enabled //
	function get_shchedule_campaign_id($conditions_array = array(),$campaign_id = 0)
	{
		$rows = array();
		$this->db->select("rec.`campaign_id`, rec.`campaign_title`,rec.`subscription_list`", false);

		$this->db->order_by('rec.`campaign_id`', 'desc');
		if($campaign_id > 0){
			$this->db->where('rec.`campaign_id` != ',$campaign_id);
		}
		#echo $this->db->_compile_select(); 
		//$this->db->join('red_email_campaigns_scheduled as rs','rs.campaign_id=rec.campaign_id','left');
		$result = $this->db->get_where('red_email_campaigns as rec', $conditions_array);
		#echo $this->db->last_query(); 

		foreach ($result->result_array() as $row)
			{
			$rows[] = $row;
		}

		return $rows;
	}
	// start adding in refrense table for ab testing
	function add_abtesting($input_array = array()){
		$this->db->insert('red_email_campaign_ab', $input_array);
	}
	// end adding in refrense table for ab testing
	// start getting ab testing detail by cb
	function get_abtesting($conditional_array = array(),$orConditional = array()){
		if(empty($orConditional)){
			$result = $this->db->get_where('red_email_campaign_ab', $conditional_array);
		}else{
			$this->db->select('*');
			$this->db->from('red_email_campaign_ab');
			$this->db->where($conditional_array);
			$this->db->or_where($orConditional); 
			$this->db->where('is_delete','0');
			$result = $this->db->get();
		}
		foreach ($result->result_array() as $row)
			{
			$rows[] = $row;
		}

		return $rows;
	}
	// end getting ab testing detail by cb
	// Start updating ab testing refrense table by cb
	/*function delete_campaignab($campaignId){
		$this->db->update('red_email_campaign_ab',array('is_delete'=>'1'),array('campaign_id'=>$campaignId));
		echo $this->db->last_query();
		$this->db->update('red_email_campaign_ab',array('is_delete'=>'1'),array('ref_campaign_id'=>$campaignId));
		echo $this->db->last_query();
	}*/
	function update_abtesting($input_array = array(),$conditional_array = array()){
		$this->db->update('red_email_campaign_ab',$input_array, $conditional_array);
		return $this->db->affected_rows();
	}
	// End updating ab testing refrense table by cb
	
	//Function to fetch campaign data
	function get_shchedule_campaign_count($conditions_array=array())
	{
		$rows=array();
		
		
  		$case=" (CASE
					WHEN campaign_status='draft' THEN `campaign_date_added` 
					WHEN campaign_status='active' THEN `email_send_date`
					WHEN campaign_status='archived' or campaign_status='active_ready' or campaign_status='ready' or campaign_status='disallow'  THEN `campaign_sheduled`
					END) as conditionalorder
					";  
		$this->db->select("rec.*,$case",false) ;
		
		#echo $this->db->_compile_select(); 
		//$this->db->join('red_email_campaigns_scheduled as rs','rs.campaign_id=rec.campaign_id','left');
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_email_campaigns as rec');		
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
	//function to create color theme
	function create_color_theme($input_array)
	{		
		$this->db->insert('red_email_campaigns_color_themes',$input_array);
		return $this->db->insert_id();
	}	
}
?>