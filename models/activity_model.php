<?php
/*
	Model class for activity log
*/
class Activity_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Activity_Model(){
		parent::__construct();
	}
	
	//function to create campaign
	function create_activity($input_array=array()){
		$this->db->insert('red_activity_log',$input_array);
		return $this->db->insert_id();
	}
	
	function create_activity_payment($input_array=array()){
		$this->db->insert('red_payment_activity',$input_array);
		return $this->db->insert_id();
	}
	
	//Function to fetch activity log
	function get_activity_log($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			if($_POST['date']){
				$date_arr=explode('/',$_POST['date']);
				$date=$date_arr[2]."-".$date_arr[0]."-".$date_arr[1];
				$this->db->like('timestamp',$date);
			}
			if($_POST['username']){
				$username=$_POST['username'];
				$this->db->like('member_username',$username);
			}
			if($_POST['campaign_name']){
				$campaign_name=$_POST['campaign_name'];
				$this->db->like('campaign_title',$campaign_name);
				$this->db->join('red_email_campaigns as rec','rec.campaign_id=ral.campaign_id ','left');
			}
			
		}
		$this->db->order_by('timestamp','desc');
		$this->db->join('red_members as rm','ral.user_id=rm.member_id');		
		
		$result=$this->db->get_where('red_activity_log as ral',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch Users count
	function get_activity_log_count($conditions_array=array()){
		$this->db->select('count(*) as count');
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			if($_POST['date']){
				$date_arr=explode('/',$_POST['date']);
				$date=$date_arr[2]."-".$date_arr[0]."-".$date_arr[1];
				$this->db->like('timestamp',$date);
			}
			if($_POST['username']){
				$username=$_POST['username'];
				$this->db->like('member_username',$username);
			}
			if($_POST['campaign_name']){
				$campaign_name=$_POST['campaign_name'];
				$this->db->like('campaign_title',$campaign_name);				
				$this->db->join('red_email_campaigns as rec','rec.campaign_id=ral.campaign_id AND ral.campaign_id > 0','left');
			}
		}
		
		$this->db->join('red_members as rm','ral.user_id=rm.member_id');
		$this->db->from('red_activity_log as ral');
		$this->db->where($conditions_array);
		$this->db->limit(100,$start);
		$result=$this->db->get();
		 
		$row=$result->result_array() ;
		
		return $row[0]['count'];
	}
}
?>