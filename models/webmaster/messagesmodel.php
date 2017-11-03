<?php
/*
	Model class for campaign
*/
class MessagesModel extends CI_Model
{
	//Constructor class with parent constructor
	 
	function MessagesModel(){
		parent::__construct();		 
	}
	//Function to fetch campaign data
	function read_messages($condition_array=array()){		  
		if(count($condition_array) > 0)
		$result=$this->db->where($condition_array);				 
		$result=$this->db->get('red_messages');				 
		return $result->result_array();
	}
	function assignable_messages($mid=0){
		$sqlMsg = "select 	message_id,message_name from red_messages where message_id not in(select message_id from red_member_message where member_id='$mid' and is_deleted=0)";
		$result=$this->db->query($sqlMsg);
		return $result->result_array();
	}
	function update_message($input_array,$conditions_array){
		$this->db->update('red_messages',$input_array,$conditions_array);
		return $this->db->affected_rows();		
	}
	function create_message($input_array){
		$this->db->insert('red_messages',$input_array);
		return $this->db->affected_rows();		
	}
	function read_member_message($mid=1){
		$this->db->select('mm.member_id, mm.message_id, mm.assigned_date, member_username, message_name');
		$this->db->from('red_member_message as mm');
		$this->db->join('red_members as mem','mm.member_id=mem.member_id');
		$this->db->join('red_messages as mes','mm.message_id=mes.message_id');
		$this->db->order_by('assigned_date','desc');
		$this->db->where(array('mm.message_id'=>$mid, 'mm.is_deleted'=>0));
		$result=$this->db->get();
		return $result->result_array();
	}		
}
?>