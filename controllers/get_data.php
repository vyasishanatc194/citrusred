<?php
class Get_data extends CI_Controller {

	function Get_data()
	{
		parent::__construct();		 	
	}

	function index(){}

	function get_file($mid,$dtype,$fname)
	{
		if($dtype == 'image'){
			$this->show_image($mid, $fname);		
		}elseif('extracted_zip_files' == $dtype){
			$this->show_template_image($mid, $fname);				
		}
	}

	function show_image($mid, $fname){
		$user_dir = $mid % 1000;
		$image_data= $this->config->item('user_public').$user_dir.'/'.$mid.'/image_bank/'.$fname; 
		if(!file_exists($image_data)) $image_data =  $this->config->item('webappassets_path').'images/pix.gif'	 ;
		$imginfo = getimagesize($image_data);
	 
		header('Content-type: '.$imginfo['mime']);
		readfile($image_data);
		die();
	}
	function show_template_image($mid, $arg1,$arg2='',$arg3='',$arg4='',$arg5='',$arg6=''){
		$user_dir = $mid % 1000;
		$urlPath = ($arg1 != 'extracted_zip_files')?$arg1 : '';
		$urlPath .= ($arg2 != '')? '/'.$arg2:'';
		$urlPath .=($arg3 != '')? '/'.$arg3:'';
		$urlPath .=($arg4 != '')? '/'.$arg4:'';
		$urlPath .=($arg5 != '')? '/'.$arg5:'';
		$urlPath .=($arg6 != '')? '/'.$arg6:'';
		$image_data= $this->config->item('user_public').$user_dir.'/'.$mid.'/extracted_zip_files/'. rawurldecode($urlPath); 
		
		if(!file_exists($image_data)) $image_data =  $this->config->item('webappassets_path').'images/pix.gif'	 ;	 
		$imginfo = getimagesize($image_data);
	 //print_r($imginfo);
		header('Content-type: '.$imginfo['mime']);
		readfile($image_data);
		die();
	}	
	// archived on 13/Jan/2016 for  http://www.red7.me/c/114146
	function show_template_image_old_not_in_use($mid, $arg1,$arg2='',$arg3='',$arg4='',$arg5='',$arg6=''){
		$user_dir = $mid % 1000;
		$urlPath = $arg1;
		$urlPath .= ($arg2 != '')? '/'.$arg2:'';
		$urlPath .=($arg3 != '')? '/'.$arg3:'';
		$urlPath .=($arg4 != '')? '/'.$arg4:'';
		$urlPath .=($arg5 != '')? '/'.$arg5:'';
		$urlPath .=($arg6 != '')? '/'.$arg6:'';
		$image_data= $this->config->item('user_public').$user_dir.'/'.$mid.'/extracted_zip_files/'. $urlPath; 
		if(!file_exists($image_data)) $image_data =  $this->config->item('webappassets_path').'images/pix.gif'	 ;	 
		$imginfo = getimagesize($image_data);
	 
		header('Content-type: '.$imginfo['mime']);
		readfile($image_data);
		die();
	}	
	function show_diy_mail_logo($mid, $arg1,$arg2='',$arg3='',$arg4='',$arg5='',$arg6=''){
		$user_dir = $mid % 1000;
		$urlPath = $arg1;
		$urlPath .= ($arg2 != '')? '/'.$arg2:'';
		$urlPath .=($arg3 != '')? '/'.$arg3:'';
		$urlPath .=($arg4 != '')? '/'.$arg4:'';
		$urlPath .=($arg5 != '')? '/'.$arg5:'';
		$urlPath .=($arg6 != '')? '/'.$arg6:'';
		$image_data= $this->config->item('user_public').$user_dir.'/'.$mid.'/'. $urlPath; 
		# die($image_data);
		if(!file_exists($image_data)) $image_data =  $this->config->item('webappassets_path').'images/pix.gif'	 ;	 
		$imginfo = getimagesize($image_data);
	 
		header('Content-type: '.$imginfo['mime']);
		readfile($image_data);
		die();
	}	
	function show_blog_img($fname){
		$image_data= rawurldecode($this->config->item('blog_files'). $fname); 
		# die($image_data);
		if(!file_exists($image_data)) $image_data =  $this->config->item('webappassets_path').'images/pix.gif'	 ;	 
		$imginfo = getimagesize($image_data);
	 
		header('Content-type: '.$imginfo['mime']);
		readfile($image_data);
		die();
	}
	function show_signup_img($mid,$fname,$bgtyp='hbg'){
		$user_dir = $mid % 1000;
		$image_data= rawurldecode($this->config->item('user_public').$user_dir.'/'.$mid.'/'.$bgtyp.'/'.$fname);
		
		// die($image_data);
		if(!file_exists($image_data)) $image_data =  $this->config->item('webappassets_path').'images/pix.gif'	 ;	 
		$imginfo = getimagesize($image_data);
	 
		header('Content-type: '.$imginfo['mime']);
		readfile($image_data);
		die();
	}	
	function show_campaign_header($is_autoresponder='c',$fname=''){
		$userId = $this->getUserIDFromHeader($fname, $is_autoresponder);
		$user_dir = $userId % 1000;
		if($is_autoresponder == 'c')
		$image_data= rawurldecode($this->config->item('user_public').$user_dir.'/'.$userId.'/email_templates/'.(int)$fname.'/'.$fname); 
		else
		$image_data= rawurldecode($this->config->item('user_public').$user_dir.'/'.$userId.'/autoresponders/'.(int)$fname.'/'.$fname); 
	#	die($image_data);
		if(!file_exists($image_data)) $image_data =  $this->config->item('webappassets_path').'images/pix.gif'	 ;	 
		$imginfo = getimagesize($image_data);
	 
		header('Content-type: '.$imginfo['mime']);
		readfile($image_data);
		die();
	}	
	function show_campaign_video($fname=''){
		$arr_fname = explode('_',$fname);
		$userId = $arr_fname[0];
		$user_dir = $userId % 1000;
		 
		$image_data= rawurldecode($this->config->item('user_public').$user_dir.'/'.$userId.'/video_img/'.$fname); 
	#	die($image_data);
		if(!file_exists($image_data)) $image_data =  $this->config->item('webappassets_path').'images/pix.gif'	 ;	 
		$imginfo = getimagesize($image_data);
	 
		header('Content-type: '.$imginfo['mime']);
		readfile($image_data);
		die();
	}	
	
	function getUserIDFromHeader($header,$is_autoresponder='c'){
		$cid = (int)$header;
		 
		$this->db->select('campaign_created_by');
		$this->db->where('campaign_id', $cid);
		if($is_autoresponder == 'c')
		$arrUserId = $this->db->get('red_email_campaigns');
		else
		$arrUserId = $this->db->get('red_email_autoresponders');
		//if id is unique we just wan one row to be returned
	#	echo $this->db->last_query();
		$data = array_shift($arrUserId->result_array());		
		$uid = (!is_null($data['campaign_created_by']))?$data['campaign_created_by']:0;
		
		return $uid;
	}
		
}
?>