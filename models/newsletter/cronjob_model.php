<?php
/*
	Model class for campaign
*/
class Cronjob_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Cronjob_Model(){
		parent::__construct();
	}	
	
	
	//function to update campaign
	function update_cronjob($input_array,$conditions_array){
		$this->db->update('red_email_campaigns_scheduled',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	
	//Function to fetch cronjob data
	function get_cronjob_data($conditions_array=array(),$rows_per_page=10,$start=0){ 
		$rows=array();
		//$this->db->order_by('campaign_scheduled_id');
		$this->db->join('red_email_campaigns as rec','rec.campaign_id=recs.campaign_id');
		$result=$this->db->get_where('red_email_campaigns_scheduled as recs',$conditions_array);
		
		foreach($result->result_array() as $row){
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch configuration data
	function get_configuration_data($conditions_array=array()){ 
		$rows=array();
		$result=$this->db->get_where('red_configuration as rc',$conditions_array);
		
		foreach($result->result_array() as $row){
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch total count of campaigns
	function get_campaign_count($conditions_array=array()){
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_email_campaigns');
	}
	
	//function to create scheduled campaign
	function create_scheduled_campaign($input_array){
		$this->db->insert('red_email_campaigns_scheduled',$input_array);
		return $this->db->insert_id();
	}
	
	//Function to fetch campaign data
	function get_shchedule_campaign_data($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();
		//$this->db->order_by('campaign_scheduled_id');
		$result=$this->db->get_where('red_email_campaigns_scheduled',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row){
			$rows[]=$row;
		}
		return $rows;
	}
	//Function to fetch cronjob data
	function get_autoresponder_cronjob_count($conditions_array=array()){		
		$this->db->select( 'count(distinct campaign_id) as ct',false);
		$this->db->join('red_email_autoresponders as rec','rec.campaign_id=recs.autoresponder_id AND rec.autoresponder_scheduled_id=recs.autoresponder_scheduled_id');
		$result=$this->db->get_where('red_autoresponder_scheduled as recs',$conditions_array);			
		return $result->row()->ct;
	}
	//Function to fetch cronjob data
	function get_autoresponder_cronjob_data($conditions_array=array(),$rows_per_page=0,$start=0){
                                   print_r("hello");
		$rows=array();
		$this->db->order_by('recs.autoresponder_scheduled_id desc');
		$this->db->join('red_email_autoresponders as rec','rec.campaign_id=recs.autoresponder_id AND rec.autoresponder_scheduled_id=recs.autoresponder_scheduled_id');
		if(trim($_POST['username']) != ''){
			$this->db->like('member_username',trim($_POST['username']));
			$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');
		}	
		if($rows_per_page > 0){
			// To Show on Autoresponder's Verify/unverify list in ADMIN
			$result=$this->db->get_where('red_autoresponder_scheduled as recs',$conditions_array,$rows_per_page,$start);		
		}else{
			// To send Autoresponders via Cronjob
                                                    
			$result=$this->db->get_where('red_autoresponder_scheduled as recs',$conditions_array);		
		}
		foreach($result->result_array() as $row){
			$rows[]=$row;
		}
		return $rows;
	}
        
      
	//Function to fetch cronjob data
	function get_autoresponder_cronjob_data_only($conditions_array=array(),$rows_per_page=0,$start=0){
                                  
		$rows=array();
		$result=$this->db->query("SELECT * FROM (`red_autoresponder_scheduled` as recs) JOIN `red_email_autoresponders` as rec ON `rec`.`campaign_id`=`recs`.`autoresponder_id` AND rec.autoresponder_scheduled_id=recs.autoresponder_scheduled_id WHERE `recs`.`is_deleted` =  0 AND `rec`.`autoresponder_scheduled_id` != 0 AND `recs`.`autoresponder_scheduled_status` =  1 AND `set_sheduled` =  0 AND `rec`.`campaign_status` =  1 AND `rec`.`is_deleted` =  0 AND `rec`.`is_status` =  0 AND `rec`.`is_verified` =  1 AND (last_run_date IS NULL OR TIMESTAMPDIFF( HOUR , last_run_date, NOW( ) ) >= 1) ORDER BY `recs`.`autoresponder_scheduled_id`");		
		
		foreach($result->result_array() as $row){
			$rows[]=$row;
		}
		return $rows;
	}
	//function to update autoresponder cronjob
	function update_autoresponder_cronjob($input_array,$conditions_array=array()){
		$this->db->update('red_autoresponder_scheduled',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Function to fetch red_autoresponder_signup data
	function get_autoresponder_signup_data($conditions_array=array()){ 
		$rows=array();
		$result=$this->db->get_where('red_autoresponder_signup as recs',$conditions_array);
				
		foreach($result->result_array() as $row){
			$rows[]=$row;
		}
		return $rows;
	}
	
	//function to add  signup subscriber contentin red_autoresponder_signup
	function add_autoresponder_signup_subscriber($input_array){
		$this->db->replace_into('red_autoresponder_signup',$input_array);
		/* $this->db->insert('red_autoresponder_signup',$input_array);*/
		return $this->db->affected_rows();
	}
	//function to update  signup subscriber contentin red_autoresponder_signup
	function update_autoresponder_signup_subscriber($input_array,$conditions_array){
		$this->db->update('red_autoresponder_signup',$input_array,$conditions_array);
		return $this->db->insert_id();
	}


// Validation functions
/**
	 *	Function Email_check
	 *
	 *	'email_check' controller function supporting email validation for import csv file
	 *
	 *	@param (string) (email)  contains email address of subscriber
	 *
	 *	@return (bool)  return true if email address validate true otherwise false
	 */
	function checkEmail($email){
		$email = trim($email);		
		if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email)){ 
			return false;
		}else{
			return true;
		}
	}
	
	/**
	 *	Function isName
	 *
	 *	'isName' controller function supporting name heading for import csv file
	 *
	 *	@param (string) (strCol)  contains name heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isName($strCol){
		$strCol = trim(strtolower($strCol));
		if($strCol != '' and (strlen(strtolower($strCol))==4) and (stripos(strtolower($strCol), 'name')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'full')!== false and stripos(strtolower($strCol), 'name')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'f')!== false and stripos(strtolower($strCol), 'l')!== false and stripos(strtolower($strCol), 'name')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'f_name')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'f-name')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'fname')!== false) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isFirstName
	 *
	 *	'isFirstName' controller function supporting first name heading for import csv file
	 *
	 *	@param (string) (strCol)  contains first name heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isFirstName($strCol){
			$strCol = trim(strtolower($strCol));
			if((stripos(strtolower($strCol), 'last')=== false) and (stripos(strtolower($strCol), 'l')=== false)){
				if($strCol != '' and (stripos(strtolower($strCol), 'first')!== false and stripos(strtolower($strCol), 'name')!== false) ){				
					return true;
				}else if($strCol != '' and (stripos(strtolower($strCol), 'given')!== false) ){				
					return true;
				}else if($strCol != '' and (stripos(strtolower($strCol), 'first')!== false) ){				
					return true;
				}else if($strCol != '' and (stripos(strtolower($strCol), 'first_name')!== false) ){				
					return true;
				}else if($strCol != '' and (stripos(strtolower($strCol), 'first-name')!== false) ){				
					return true;
				}/* else if($strCol != '' and (stripos(strtolower($strCol), 'f')!== false and stripos(strtolower($strCol), 'name')!== false) ){				
					return true;
				} */else if($strCol != '' and (stripos(strtolower($strCol), 'f_name')!== false) ){				
					return true;
				}else if($strCol != '' and (stripos(strtolower($strCol), 'f-name')!== false) ){				
					return true;
				}else if($strCol != '' and (stripos(strtolower($strCol), 'fname')!== false) ){				
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
	}
	
	/**
	 *	Function isLastName
	 *
	 *	'isLastName' controller function supporting last name heading for import csv file
	 *
	 *	@param (string) (strCol)  contains last name heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isLastName($strCol){
		$strCol = trim(strtolower($strCol));
		if((stripos(strtolower($strCol), 'first')=== false) and (stripos(strtolower($strCol), 'f')=== false)){
			if($strCol != '' and (stripos(strtolower($strCol), 'last')!== false and stripos(strtolower($strCol), 'name')!== false) ){
				return true;
			}else if($strCol != '' and (stripos(strtolower($strCol), 'family')!== false) ){
				return true;
			}else if($strCol != '' and (strlen(strtolower($strCol))==4) and (stripos(strtolower($strCol), 'last')!== false) ){
				return true;
			}else if($strCol != '' and (stripos(strtolower($strCol), 'last_name')!== false) ){
				return true;
			}else if($strCol != '' and (stripos(strtolower($strCol), 'last-name')!== false) ){
				return true;
			}/* else if($strCol != '' and (stripos(strtolower($strCol), 'l')!== false and stripos(strtolower($strCol), 'name')!== false) ){
				return true;
			} */else if($strCol != '' and (stripos(strtolower($strCol), 'l_name')!== false) ){
				return true;
			}else if($strCol != '' and (stripos(strtolower($strCol), 'l-name')!== false) ){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	/**
	 *	Function isState
	 *
	 *	'isState' controller function supporting state heading for import csv file
	 *
	 *	@param (string) (strCol)  contains state heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isState($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'state')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'province')!== false) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isCountry
	 *
	 *	'isCountry' controller function supporting country heading for import csv file
	 *
	 *	@param (string) (strCol)  contains country heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isCountry($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'country')!== false) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isCity
	 *
	 *	'isCity' controller function supporting city heading for import csv file
	 *
	 *	@param (string) (strCol)  contains city heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isCity($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'city')!== false) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isZipcode
	 *
	 *	'isZipcode' controller function supporting zip code heading for import csv file
	 *
	 *	@param (string) (strCol)  contains zip code heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isZipcode($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'zip')!== false and stripos(strtolower($strCol), 'code')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'zip')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'code')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'postcode')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'post')!== false and stripos(strtolower($strCol), 'code')!== false) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isBirthday
	 *
	 *	'isBirthday' controller function supporting Birthday heading for import csv file
	 *
	 *	@param (string) (strCol)  contains zip code heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isBirthday($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'birthday')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'b-day')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'bday')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'date')!== false and stripos(strtolower($strCol), 'of')!== false and stripos(strtolower($strCol), 'birth')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'Date_of_Birth')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'Date-of-Birth')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'birth')!== false and stripos(strtolower($strCol), 'day')!== false ) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isCompany
	 *
	 *	'isCompany' controller function supporting company heading for import csv file
	 *
	 *	@param (string) (strCol)  contains comapny heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isCompany($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'company')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'company')!== false and stripos(strtolower($strCol), 'name')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'business')!== false and stripos(strtolower($strCol), 'name')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'business')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'organization')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'organization')!== false and stripos(strtolower($strCol), 'name')!== false ) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isPhone
	 *
	 *	'isPhone' controller function supporting phone heading for import csv file
	 *
	 *	@param (string) (strCol)  contains phone heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isPhone($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'phone')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone')!== false and stripos(strtolower($strCol), 'no')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone_number')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone-number')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone')!== false and stripos(strtolower($strCol), '#')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone_#')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone-#')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'telephone')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'telephone')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'telephone')!== false and stripos(strtolower($strCol), '#')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'Telephone_Number')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'Telephone-Number')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'tel')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'tel.')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'contact')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'contact')!== false and stripos(strtolower($strCol), 'no')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'cell')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'mobile')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isAddress
	 *
	 *	'isAddress' controller function supporting  address heading for import csv file
	 *
	 *	@param (string) (strCol)  contains address heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isAddress($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'address')!== false and stripos(strtolower($strCol), 'email')=== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'address')!== false and stripos(strtolower($strCol), 'mail')=== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'physical')!== false and stripos(strtolower($strCol), 'address')!== false) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *	Function isEmailAddress
	 *
	 *	'isEmailAddress' controller function supporting  email address heading for import csv file
	 *
	 *	@param (string) (strCol)  contains email address heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isEmailAddress($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'email')!== false and stripos(strtolower($strCol), 'address')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'email')!== false and stripos(strtolower($strCol), 'id')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'mail')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'email_address')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'email-address')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'email_id')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'email-id')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'e-mail')!== false and stripos(strtolower($strCol), 'address')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'e-mail')!== false and stripos(strtolower($strCol), 'id')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'e-mail')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'email')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'eml')!== false) ){
			return true;
		}else{
			return false;
		}
	}
	

	
}
?>