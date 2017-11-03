<?php
/**
* A Campaign_email_track_restorage_Model class
*
* This class is for interaction with datbase
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Campaign_email_track_restorage_Model extends CI_Model
{
	#Constructor class with parent constructor
	function Campaign_email_track_restorage_Model(){
		parent::__construct();
	}
	/**
		Function get_campaign_data to fetch campaign list 
		@param array conditions_array :for where condition
		@return array rows : contain list of campaign
	*/
	function get_campaign_data($dt_gap){
		$rows=array();
		//$this->db->select('campaign_id,campaign_created_by');
		//$result=$this->db->get_where('red_email_campaigns as rec',$conditions_array,2, 0);
		
		$result=$this->db->query("SELECT `campaign_id`, `campaign_created_by`,email_send_date FROM (`red_email_campaigns` as rec)inner join red_members m on rec.campaign_created_by=m.member_id WHERE  `email_send_date` IS NOT NULL AND DATEDIFF(CURDATE(),email_send_date)> $dt_gap  AND `campaign_status` = 'active' AND `is_restore` = '0' limit 3");
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	/**
		Function email_read_count to count number of read email
		@param array conditions_array :for where condition
		@return array rows : contain read email count
	*/
	function email_read_count($conditions_array=array()){
		$this->db->select_sum('email_track_read', 'email_track_read');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows=$row['email_track_read'];
		}
		return	$rows;
	}
	/**
		Function email_sent_count to count number of send email
		@param array conditions_array :for where condition
		@return array rows : contain send email count
	*/
	function email_sent_count($conditions_array=array()){
		$this->db->select_sum('email_sent', 'email_sent');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows=$row['email_sent'];
		}
		return	$rows;
	}
	/**
		Function email_complaint_count to count number of complaint email
		@param array conditions_array :for where condition
		@return array rows : contain complaint email count
	*/
	function email_complaint_count($conditions_array=array()){
		$this->db->select_sum('email_track_complaint', 'email_track_complaint');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows=$row['email_track_complaint'];
		}
		return	$rows;
	}
	/**
		Function email_bounce_count to count number of bounce email
		@param array conditions_array :for where condition
		@return array rows : contain bounce email count
	*/
	function email_bounce_count($conditions_array=array()){
		$this->db->select_sum('email_track_bounce', 'email_track_bounce');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows=$row['email_track_bounce'];
		}
		return	$rows;
	}
	/**
		Function email_unsubscribe_count to count number of unsubscribe email
		@param array conditions_array :for where condition
		@return array rows : contain bounce email count
	*/
	function email_unsubscribe_count($conditions_array=array()){
		$this->db->select_sum('email_track_unsubscribes', 'email_track_unsubscribes');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows=$row['email_track_unsubscribes'];
		}
		return	$rows;
	}
	/**
		Function email_click_count to count number of click link email
		@param array conditions_array :for where condition
		@return array rows : contain click link email count
	*/
	function email_click_count($conditions_array=array()){
		$this->db->select_sum('email_track_click', 'email_track_click');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows=$row['email_track_click'];
		}
		return	$rows;
	}
	/**
		Function email_forward_count to count number of forward link email
		@param array conditions_array :for where condition
		@return array rows : contain forward email count
	*/
	function email_forward_count($conditions_array=array()){
		$this->db->select_sum('email_track_forward', 'email_track_forward');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows=$row['email_track_forward'];
		}
		return	$rows;
	}
	/**
		Function fetch_email_address_list to get email list
		@param array conditions_array :for where condition
		@return array rows : contain email address
	*/
	function fetch_email_address_list($conditions_array=array()){
		$this->db->select('subscriber_email_address');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row['subscriber_email_address'];
		}
		return	$rows;
	}
	/**
		Function fetch_forward_email_address_list to get forward email list
		@param array conditions_array :for where condition
		@return array rows : contain email address and number of forward email in array
	*/
	function fetch_forward_email_address_list($conditions_array=array()){
		$this->db->select('subscriber_email_address');
		$this->db->select('email_track_forward');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return	$rows;
	}
	/**
		Function fetch_click_link_list to get click link list
		@param array conditions_array :for where condition
		@return array rows : contain click links and number of click on links in array
	*/
	function fetch_click_link_list($conditions_array=array()){
		$this->db->select('`actual_url`');
		$this->db->select('counter');
		$this->db->from('red_click_rate as rct')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return	$rows;
	}
	/**
		Function fetch_click_link_list to Insert email track values in email restore table
		@param array conditions_array :for input values		
	*/
	function add_email_track_restorage($input_array){
		$this->db->replace_into('red_email_track_freezed',$input_array);
		return $this->db->insert_id();
	}
	/**
		Function delete_email_track to delete email track values 
		@param array conditions_array :for where condition
	*/
	function delete_email_track($conditions_array=array()){
		$this->db->delete('red_email_track',$conditions_array);
	}
	/**
		Function delete_email_track_list to delete email track list values
		@param array conditions_array :for where condition
	*/
	function delete_email_track_list($conditions_array=array()){
		$this->db->delete('red_email_campaigns_scheduled',$conditions_array);
	}
	/**
		Function delete_email_click_rate to delete click link values
		@param array conditions_array :for where condition
	*/
	function delete_email_click_rate($conditions_array=array()){
		$this->db->delete('red_click_rate',$conditions_array);
	}
	/**
		Function update_campaign to update campaign
		@param array conditions_array :for where condition
	*/
	function update_campaign($input_array,$conditions_array){
		$this->db->update('red_email_campaigns',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	/**
		Function fetch_email_report_from_backup to fetch campaign stat from backup table
		@param array conditions_array :for where condition
		@return array rows :contain campaign stat in an array
	*/
	function fetch_email_report_from_backup($conditions_array=array()){
		$this->db->from('red_email_track_freezed as ret')->where($conditions_array);
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return	$rows;
	}
}
?>