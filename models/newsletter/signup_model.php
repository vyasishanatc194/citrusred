<?php
/*
	Model class for signup form
*/
class Signup_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Signup_Model()
	{
		parent::__construct();
	}

	//Function to create new signup form
	function create_signup($input_array){		
		$this->db->insert('red_signup_form',$input_array);
		return $this->db->insert_id();
	}

	//Function to update signup form
	function update_signup($input_array,$conditions_array){
		$this->db->update('red_signup_form',$input_array,$conditions_array);
		return $this->db->affected_rows();		
	}

	//Function to delete signup form
	function delete_signup($conditions_array)
	{
		//$this->db->delete('red_email_subscriptions',$conditions_array);
		$this->db->update('red_signup_form',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}

	//Function to fetch signup form
	function get_signup_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->order_by('id','asc');
		$result=$this->db->get_where('red_signup_form',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$member_id = $row['member_id'];

			if($this->db->query("select member_id from red_members where member_id='$member_id' and is_deleted=0")->num_rows() > 0)
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch signup form
	function get_signupform_toverify($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();
		$this->db->order_by('id','desc');
		$result=$this->db->get_where('red_signup_form',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row){
			$member_id = $row['member_id'];
			if($this->db->query("select member_id from red_members where member_id='$member_id' and is_deleted=0")->num_rows() > 0)
			$rows[]=$row;
		}
		return $rows;
	}

	//Function to fetch signup forms  count
	function get_signup_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_signup_form');
	}

	//function to create new autoresponder
	function create_autoresponder($input_array)
	{
		$this->db->insert('red_email_autoresponders',$input_array);
		return $this->db->insert_id();
	}

	//function to update autoresponder info
	function update_autoresponder($input_array,$conditions_array)
	{
		$this->db->update('red_email_autoresponders',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}

	//Function to fetch subscription data
	function get_autoresponder_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$result=$this->db->get_where('red_email_autoresponders',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//function to fetch email subscriber
	function get_signupform_stats_contacts($arrFormStats,$mid){
		$rows=array();
		if(count($arrFormStats) > 0){
			foreach($arrFormStats as $each_contact){
				$subscriber_id = $each_contact['subscriber_id'];
				$rsContacts = $this->db->query("select * from red_email_subscribers where subscriber_id='$subscriber_id'");					 			
				foreach($rsContacts->result_array() as $row){
					$rows[]=$row;
				}
				$rsContacts->free_result();
			}
		}
		return $rows;
	}
	//function to fetch form subscribers
	function get_signupform_stats($conditions_array=array(),$rows_per_page=0,$start=0){
		$rows=array();
		$this->db->select('distinct subscriber_id', false);		
		$this->db->from('red_signup_form_stats');		
		if($rows_per_page>0){
			$this->db->limit($rows_per_page, $start);
		}
		$this->db->where($conditions_array);		
		$result=$this->db->get();			
		foreach($result->result_array() as $row){
			$rows[]=$row;
		}
		$result->free_result();
		return $rows;
	}
}
?>
