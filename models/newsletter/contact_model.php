<?php
/**
  *	Model class for contacts to be used wherever contact related info will be needed   
  * Created by pravinjha@gmail.com on 14Feb 2013
 */
class Contact_model extends CI_Model
{
	
	function contact_model(){
		parent::__construct();
	}
	/**
	 *	Function update_subscriber
	 *
	 *	Function to get contacts count
	 *
	 *	@param (array) (conditions_array)  conditions to checked with database with conditions
	 *
	 *	@return (int)	return updated subscriber id
	 */
	function getContactsCount($condition_array){
		$this->db->select('count(subscriber_id) as tot_contacts',false);
		$this->db->from('red_email_subscribers as res');
		$this->db->where($condition_array, NULL, FALSE);
		$rsContacts = $this->db->get();		
		$retval = $rsContacts->row()->tot_contacts;
		$rsContacts->free_result();
		return $retval;
	}
	function get_contacts_count_in_list($conditions_array=array(),$subscription_id=0){
		if($subscription_id > 0){		 
			$this->db->select('count(res.subscriber_id) as totContact',false);
			$this->db->from('red_email_subscription_subscriber as ress');
			$this->db->join('red_email_subscribers as res','res.subscriber_id =ress.subscriber_id', 'right');			 
			$this->db->where($conditions_array, NULL, FALSE);
			$this->db->where(array('ress.subscription_id'=>$subscription_id));
			$result=$this->db->get();
			$retval = $result->row()->totContact;
			$result->free_result();
		}else{		
			$retval = $this->getContactsCount($conditions_array);		
		}
		return $retval;
	}
	function get_contacts_count_in_selected_lists($conditions_array=array(),$subscription_id=array()){			 
		$this->db->select('count(distinct res.subscriber_id) as totContact',false);
		$this->db->from('red_email_subscription_subscriber as ress');
		$this->db->join('red_email_subscribers as res','res.subscriber_id =ress.subscriber_id', 'right');			 
		$this->db->where($conditions_array);			
		if(count($subscription_id)>0){
			if(!(in_array('-'.$this->session->userdata('member_id'),$subscription_id))){			
				$this->db->where_in('ress.subscription_id', $subscription_id);
			}
		}			
		$result=$this->db->get();			
		$retval = $result->row()->totContact;	
		$result->free_result();
		return $retval;	
	}
	function contacts_count_in_lists($mid=0, $conditions_array=array(),$subscription_id=array()){			 
		$this->db->select('count(distinct res.subscriber_id) as totContact',false);
		$this->db->from('red_email_subscribers as res');					 
		$this->db->where($conditions_array);			
		if(count($subscription_id)>0){
			if(!(in_array(intval(0 - $mid),$subscription_id))){	
				$this->db->join('red_email_subscription_subscriber as ress','ress.subscriber_id =res.subscriber_id', 'left');
				$this->db->where_in('ress.subscription_id', $subscription_id);
			}
		}			
		$result=$this->db->get();			
		$retval = $result->row()->totContact;	
		$result->free_result();
		return $retval;	
	}
	
	function get_contacts_detail_in_selected_lists($conditions_array=array(),$subscription_id=array()){

		$this->db->select('distinct res.subscriber_id, res.subscriber_email_address, res.subscriber_first_name, res.subscriber_last_name, res.subscriber_state, res.subscriber_zip_code, res.subscriber_country, res.subscriber_company, res.subscriber_city, res.subscriber_dob, res.subscriber_phone, res.subscriber_address,res.subscriber_date_added, res.subscriber_ip, res.subscriber_extra_fields',false);
		$this->db->from('red_email_subscribers as res');
		if(!(in_array('-'.$this->session->userdata('member_id'),$subscription_id))){
			$this->db->join('red_email_subscription_subscriber as ress','res.subscriber_id =ress.subscriber_id', 'left');			 
		}
		$this->db->where($conditions_array, NULL, FALSE);		
		if(count($subscription_id)>0){
			if(!(in_array('-'.$this->session->userdata('member_id'),$subscription_id))){			
				#$this->db->where_in('ress.subscription_id', $subscription_id);
				$this->db->where('ress.subscription_id', $subscription_id[0]);
			}
		}		
		$result=$this->db->get();			
		$retval = $result->result_array();		
		$result->free_result();
		return $retval;	
	}
	/**
	* Function to delete contacts from List
	* It is used in /controllers/newsletters/subscriber.php and function subscriber_delete()
	*/
	
	function remove_contacts_from_list($mid, $list_id){
		$delete_contacts = 0 ;
		$qryCheckListOwnership = "select subscription_id from `red_email_subscriptions` where `subscription_id`='$list_id' and `subscription_created_by` = '$mid'";
		if($this->db->query($qryCheckListOwnership)->num_rows() > 0){
			if(isset($_POST['email_search']) and trim($_POST['email_search'])!=''){
				$srch=$_POST['email_search'];
				$qry = "select `subscriber_id` from `red_email_subscribers` where `subscriber_created_by`='$mid' and `subscriber_status`=1 and ( subscriber_email_address LIKE '%$srch%' OR subscriber_first_name LIKE '%$srch%' OR subscriber_last_name LIKE '%$srch%' OR subscriber_extra_fields LIKE '%$srch%')";
				$rsContacts = $this->db->query($qry);
				if($rsContacts->num_rows()>0){
					foreach($rsContacts->result_array() as $row){
						$subscriber_id = $row['subscriber_id'];			
						$qryContactsFromListOnly = "delete from `red_email_subscription_subscriber` where `subscriber_id` ='$subscriber_id'";
						$this->db->query($qryContactsFromListOnly);		 
						$delete_contacts = $this->db->affected_rows(); 				
					}
				}
			}else{
				$qryDeleteAllFromListOnly = "delete from `red_email_subscription_subscriber` where `subscription_id` ='$list_id'";
				$this->db->query($qryDeleteAllFromListOnly);		 
				$delete_contacts = $this->db->affected_rows(); 
			}
		}
			return $delete_contacts;
	}
	/**
	* Function to delete contacts from ALL-Lists
	* It is used in /controllers/newsletters/subscriber.php and function subscriber_delete()
	*/
	
	function remove_contacts_from_all_lists($mid, $listid){
		$delete_contacts = 0 ;		 
		$whereClause ='';
		if(isset($_POST['email_search']) and trim($_POST['email_search'])!=''){
			$srch=$_POST['email_search'];
			$whereClause = " and ( subscriber_email_address LIKE '%$srch%' OR subscriber_first_name LIKE '%$srch%' OR subscriber_last_name LIKE '%$srch%' OR subscriber_extra_fields LIKE '%$srch%')";			
		}	
		if($listid < 0)
		$qry = "update `red_email_subscribers` set `is_deleted`=1, `status_change_date`=curdate() where `subscriber_created_by`='$mid' and `subscriber_status`=1 and `is_deleted`=0 $whereClause";
		else
		$qry = "update `red_email_subscribers` s INNER JOIN `red_email_subscription_subscriber` ss ON s.subscriber_id=ss.subscriber_id and ss.`subscription_id`='$listid'  set `is_deleted`=1, `status_change_date`=curdate() where `subscriber_created_by`='$mid' and `subscriber_status`=1 and `is_deleted`=0 $whereClause";
			
		$this->db->query($qry);
		return $this->db->affected_rows();
	}
	 
	
}
?>
