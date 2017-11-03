<?php
/*
	Model class for email track
*/
class Emailreport_Model extends CI_Model
{
	//Constructor functon for subscriber with parent constructor
	function Emailreport_Model()
	{
		parent::__construct();
	}
	
	//function to fetch email report
	function get_emailreport_data($conditions_array=array(),$like=false,$rows_per_page=10,$start=0,$paging=false)
	{
		$rows=array();
		if($like){
			if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
				if($_POST['subscriber_email_address']){
					$subscriber_email_address=$_POST['subscriber_email_address'];
					$this->db->like('ret.subscriber_email_address',$subscriber_email_address);
				}			
				if($_POST['keyword']!=""){
					$keyword=$this->escape_str($_POST['keyword'],true);
					$this->db->where('(ret.`subscriber_email_address` LIKE \'%'.$keyword.'%\' )');
					$this->db->or_where('(`subscriber_first_name` LIKE \'%'.$keyword.'%\' )');
				}
			}
		}
		$this->db->join('red_email_subscribers as res','res.subscriber_email_address=ret.subscriber_email_address AND res.subscriber_id=ret.subscriber_id');
		$this->db->from('red_email_track as ret')->where($conditions_array);
		if($paging){			 
			$this->db->limit($rows_per_page,$start);			
		}
		$this->db->order_by('ret.subscriber_email_address');
		
		$result=$this->db->get();
		
		//echo $this->db->last_query();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//function to fetch email queue
	function get_emailqueue_data($conditions_array=array(),$like=false,$rows_per_page=10,$start=0,$paging=false)
	{
		$rows=array();
		if($like){
			if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
				if($_POST['subscriber_email_address']){
					$subscriber_email_address=$_POST['subscriber_email_address'];
					$this->db->like('req.subscriber_email_address',$subscriber_email_address);
				}			
				if($_POST['keyword']!=""){
					$keyword=$this->escape_str($_POST['keyword'],true);
					$this->db->where('(req.`subscriber_email_address` LIKE \'%'.$keyword.'%\' )');
					$this->db->or_where('(`subscriber_first_name` LIKE \'%'.$keyword.'%\' )');
				}
			}
		}
		$this->db->join('red_email_subscribers as res','res.subscriber_email_address=req.subscriber_email_address AND res.subscriber_id=req.subscriber_id');
		$this->db->from('red_email_queue as req')->where($conditions_array);
		if($paging){			 
			$this->db->limit($rows_per_page,$start);			
		}
		$this->db->order_by('req.subscriber_email_address');
		
		$result=$this->db->get();
		
		//echo $this->db->last_query();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	function escape_str($str,$like=false)
	{
		if (is_array($str))
        {
            foreach($str as $key => $val)
            {
                $str[$key] = $this->escape_str($val, $like);
            }
           
            return $str;
        }

        if (function_exists('mysql_real_escape_string') AND is_resource($this->conn_id))
        {
            $str = mysql_real_escape_string($str, $this->conn_id);
        }
        elseif (function_exists('mysql_escape_string'))
        {
            $str = mysql_escape_string($str);
        }
        else
        {
            $str = addslashes($str);
        }
        
        // escape LIKE condition wildcards
        if ($like === TRUE)
        {
            $str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
        }
        
        return $str;
	}

}
?>