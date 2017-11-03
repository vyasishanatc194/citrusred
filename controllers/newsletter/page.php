<?php
/*
	Controller class for pages 
	It have controller functions for pages management.
*/
class Page extends CI_Controller
{

	/*
		Contructor for controller.
		It checks user session and redirects user if not logged in
	*/
	function __construct(){
        parent::__construct();
		if($this->session->userdata('member_id')=='')
			redirect('user/login');	

		$this->load->model('newsletter/Page_Model');	
		$this->load->model('newsletter/Campaign_Model');	
		$this->load->model('newsletter/Autoresponder_Model');
		$this->load->model('UserModel');
		$this->load->model('Activity_Model');
		$this->load->model('newsletter/Campaign_Autoresponder_Model');
		
		$this->load->helper('simple_html_dom');
		$this->load->helper('htmltotext');
		// set upload file path
		$this->upload_path=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
	}	
	
	function get_content($id){
		$fetch_conditions_array=array('id'=>$id);
		$page_data=$this->Page_Model->get_page_data($fetch_conditions_array);
		echo $page_data[0]['page_content'];
	}
	
	/**
		Function publish_content to save email content in database
	**/
	function publish_content($page_id,$email_campaign_id,$is_autoresponder=0){
		$page_html=  $this->input->post('campaign_content');		
		$campaign_title=$this->input->post('campaign_title');
		//$campaign_title = mb_convert_encoding($campaign_title, 'HTML-ENTITIES', 'UTF-8');
//		$header_html='<!DOCTYPE html><html>
		$header_html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
		<head><title>RedCappi Campaign</title><meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
		<style type="text/css">
		#email_template_table{ font-family: Arial,Helvetica,sans-serif;} 
		.edit_offer{max-width:555px;} 
		.header_img{display:block} 
		.text_img_paragraph{text-align:left;} 
		.text_img_outer_div{text-align:left;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;} 
		.header-text{text-align:left !important;font-size:18px;font-weight:bold;} 
		.active_image_option{display:block;text-align:left;} 		
		.text-paragraph-container{padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;} 
		.empty_text{text-align:center;}
		.social_li{list-style-type: none;padding-left:3px;padding-right: 3px;padding-top:0px;padding-bottom:0px;}
		.container-div{border-color:transparent;border-style: solid; border-width: 15px 4px 4px;width: 100%;}
		.edit_offer_div{background-color:#ffffff;}
		.youtube_image_caption,	.image_caption{padding-bottom:0px;padding-left:0px;display:block;text-align:left;font-size:11px} 
		.edit_offer{padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;}
		</style></head><body class="body_style">';
			$page_html=str_replace("©","&copy;",$page_html);			 
			
			$email_content=htmlspecialchars($page_html);
			$page_html=$header_html.trim($page_html);
			
		 
			$page_html=str_replace("&nbsp;", "[xxxspacexxx]",$page_html);
			$page_html=$this->automatice_css_inliner($page_html);
			$page_html=str_replace("[xxxspacexxx]", "&nbsp;",$page_html);			
			$page_html=remove_extra_html($email_campaign_id, $page_html, $is_autoresponder);
			$page_html=str_replace("</body>", "",$page_html);
			$page_html=str_replace("</html>", "",$page_html);
			
			$text_html=html2text($page_html);
			
			$page_html=str_replace("©","&copy;",$page_html);
			// echo $page_html;
		
			if($is_autoresponder!=1){
				//Fetch campaign data from database by campaign ID
				$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$email_campaign_id));
			}
			/*********************************************/
			if(!is_null($campaign_array[0]['email_subject']) and trim($campaign_array[0]['email_subject']) !=''){
				$newSubject = $campaign_array[0]['email_subject'];
			}elseif($campaign_title != 'Unnamed'){
				$newSubject = $campaign_title;				
			}else{	
			//	$newSubject = 'Unnamed';
			}	
			$input_array=array('campaign_content'=>$page_html,'campaign_title'=>$campaign_title,'email_subject'=>$newSubject,'campaign_outer_bg'=>$_POST['campaign_outer_bg'],	'campaign_email_content'=>$email_content,'campaign_text_content'=>$text_html,'campaign_template_option'=>'3', 'is_status'=>'0');
			if($is_autoresponder==1){				
				// Update campaign by data posted by user
				$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$email_campaign_id));
			}else{				
				// Update campaign by data posted by user
				$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$email_campaign_id));
				if($campaign_array[0]['is_status']==1){					
					# create array for insert values in activty table					
					$this->Activity_Model->create_activity(array('user_id'=>$this->session->userdata('member_id'), 'activity'=>'campaign_created', 'campaign_id'=>$email_campaign_id));
				}
			}		
			if($is_autoresponder==1){				 
				$this->Campaign_Autoresponder_Model->encode_url($email_campaign_id,$page_html,true);
			}else{				 
				$this->Campaign_Autoresponder_Model->encode_url($email_campaign_id,$page_html);
			}			
	}
	
	 
	
	/**
	 *	Function Automatice_css_inliner
	 *
	 *	'automatice_css_inliner' controller function for converting css to inline css
	 *
	 *	@param (string) (html_content)  contains html content
	 *	
	 */
	function automatice_css_inliner($html_content=""){
		$this->load->library('CSSToInlineStyles');// Load library for converting css to inline css
		$dom = new DOMDocument();
		if(isset($_POST['paste_code'])){
			$html=$_POST['paste_code'];
		}else if(isset($_POST['campaign_import_url'])){
			$url=$this->input->get_post('campaign_import_url', true);
			$html=$this->get_html_from_url($url);
		}else{
			$html=$html_content;
		}
		$dom->recover = true;
		$dom->strictErrorChecking = false;
			
		libxml_use_internal_errors(true);
		@$dom->loadHTML($html); // Can replace with $dom->loadHTML($str);
		libxml_clear_errors();
		/**
			// get stylesheet of extrnal files
		**/
		$link_tags = $dom->getElementsByTagName('style');
		
		$css="";
		for($i = 0; $i < $link_tags->length; $i++){
			$css.=$link_tags->item($i)->nodeValue;
			$link_tags->item($i)->nodeValue="";
		}
		/**
			 // get stylesheet of link tag
		**/
		$link_tags = $dom->getElementsByTagName('link');
		for($i = 0; $i < $link_tags->length; $i++){			
			$url= $link_tags->item($i)->getAttribute('href');
			$css.=file_get_contents($url);
		}
		// convert css to inline style
		$this->csstoinlinestyles->setHTML($html);
		$this->csstoinlinestyles->setCSS($css);
		$this->csstoinlinestyles->setCleanup(false);
		// grab the processed HTML
		$processedHTML =  $this->csstoinlinestyles->convert();
		$processedHTML= preg_replace ('/<link[^>]+\>/i', "", $processedHTML); // remove link tag css 
		$processedHTML= preg_replace ('/(\\s*)(<script\\b[^>]*?>)([\\s\\S]*?)<\\/script>(\\s*)/i'
	, "", $processedHTML); // remove javascript from page
		$processedHTML= preg_replace ('/<script[^>]+\>/i', "", $processedHTML); // remove javascript from page
		$processedHTML= preg_replace ('/<meta http-equiv="refresh"[^>]+\>/i', "", $processedHTML); // remove meta refresh from page


		return preg_replace ('|\<style.*\>(.*\n*)\</style\>|isU', "", $processedHTML); // return filtter html content
	}	
	
	function get_template($id){
		$fetch_conditions_array=array('id'=>$id);
		$template_data=$this->Page_Model->get_template_data($fetch_conditions_array);
		
		echo $template_data[0]['template_content'];
	}
	
	function get_all_block_data_for_ajax($page_id,$random=0){
		$block_data_array=array();
		$block_all_data_count=$this->Campaign_Model->get_template_blocks_names_and_content_count(array('page_id'=>$page_id));
		
		$block_all_data=$this->Campaign_Model->get_template_blocks_names_and_content_data(array('page_id'=>$page_id),$block_all_data_count);
		
		//var_dump($block_all_data);
		
		for($i=0;$i<count($block_all_data);$i++)
		{
			$block_name=$block_all_data[$i]['block_name'];
			if($block_all_data[$i]['block_name']=="footer"){
			// Fetch user data from database			
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
			}
			$block_data_array["{$block_name}"]=$block_all_data[$i]['block_content'];
		}
		
		
		echo json_encode( $block_data_array);
		
	}
	
	function get_pages_array_for_ajax($website_id,$random){
		$fetch_conditions_array=array(
		'site_id'=>$website_id,
		
		'is_deleted'=>'No',
		);
		
		$total_pages=$this->Page_Model->get_page_count($fetch_conditions_array);
		
		$pages=$this->Page_Model->get_page_data($fetch_conditions_array,$total_pages);
		
		for($i=0;$i<count($pages);$i++)
		{
			$pages_arr[$pages[$i]['id']]=$pages[$i]['name'];
		}
		
		echo json_encode($pages_arr);
	}
	
	function get_pages_listing_for_ajax($website_id,$random){
		$fetch_conditions_array=array(
		'site_id'=>$website_id,
		'is_deleted'=>'No',
		);
		
		$total_pages=$this->Page_Model->get_page_count($fetch_conditions_array);
		
		$pages=$this->Page_Model->get_page_data($fetch_conditions_array,$total_pages);
		
		$page_str='<table id="page_listing_table"><tr><th>Page Name</th><th>Page Title</th><th>Published</th><th>Edit</th><th>Delete</th><th>Reorder</th></tr>';
		
		for($i=0;$i<count($pages);$i++)
		{
			$page_str.='<tr id="page_tr_'.$pages[$i]['id'].'" class="page_listing_row"><td>'.$pages[$i]['name'].'</td><td>'.$pages[$i]['title'].'</td><td>'.$pages[$i]['is_published'].'</td><td><a href="javascript:;" onclick="editPage('.$pages[$i]['id'].')">Edit</a></td><td><a href="javascript:;" onclick="deletePage('.$pages[$i]['id'].')">Delete</a></td><td>move</td></tr>';
		}
		$page_str.='</table>';
		
		echo $page_str;
	}
	
	function update_page_order($random){
		$page_array=explode(',',$this->input->post('page_data'));
		$k=1;
		for($i=0;$i<count($page_array);$i++)
		{
			$id=str_replace('page_tr_','',$page_array[$i]);
			$this->Page_Model->update_page(array('page_position'=>$k),array('id'=>$id));
			$k++;
		}
	}
	/***********function to save background color of page************************/
	function saveColor($page_id){
		$conditions_array=array('id'=>$page_id);
		$template_id=trim($this->input->post('current_template_id'));		
		$background_color_block_data=$this->Campaign_Model->get_background_color_blocks_data(array('red_background_color_template_id'=>-1,'red_background_color_block_name'=>$this->input->post('element_name',true)));
		
		for($i=0;$i<count($background_color_block_data);$i++)
		{
			$block_content=urldecode($this->input->post($background_color_block_data[$i]['red_background_color_block_name'],false));
			$fetch_condition_array=array('red_background_color_page_id'=>$page_id,'red_background_color_block_name'=>$background_color_block_data[$i]['red_background_color_block_name']);
				
			$block_content_data=$this->Campaign_Model->get_background_color_blocks_content_data($fetch_condition_array);
				
				if(!count($block_content_data))
				{
					$insert_array=array('red_background_color_page_id'=>$page_id,'red_background_color_block_name'=>$background_color_block_data[$i]['red_background_color_block_name'],'red_background_color_block_content'=>$block_content);
					echo $this->Campaign_Model->add_background_color_content($insert_array);
				}
				else
				{
					$update_array=array('red_background_color_block_content'=>$block_content);
					$conditions_array=array('red_background_color_page_id'=>$page_id,'red_background_color_block_name'=>$background_color_block_data[$i]['red_background_color_block_name']);
					$this->Campaign_Model->update_background_color_content($update_array,$conditions_array);
				}
		}
	}
	/*************function to get block background color using ajax***********************/
	function get_all_block_background_color_for_ajax($page_id,$random=0){
		$block_data_array=array();
		$block_all_data_count=$this->Campaign_Model->get_background_color_blocks_names_and_content_count(array('red_background_color_page_id'=>$page_id));
		$block_all_data=$this->Campaign_Model->get_background_color_blocks_names_and_content_data(array('red_background_color_page_id'=>$page_id),$block_all_data_count);
		
		//var_dump($block_all_data);
		
		for($i=0;$i<count($block_all_data);$i++)
		{
			$block_name=$block_all_data[$i]['red_background_color_block_name'];
			$block_data_array["{$block_name}"]=$block_all_data[$i]['red_background_color_block_content'];
		}		
		echo json_encode( $block_data_array);
	}
	/*
		*function to reset color of selected theme*
	*/
	function resetColor($page_id,$random=0){
		$this->Autoresponder_Model->reset_color(array('red_background_color_page_id'=>$page_id));
	}	
}
?>