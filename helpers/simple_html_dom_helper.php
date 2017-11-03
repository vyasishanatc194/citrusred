<?php
/************************Plugin php mailer for sending mail************************************/

if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once("simple_html_dom/simple_html_dom.php");	
//require_once("htmlfixer.class.php");	
require_once("format.php");	
function remove_extra_html($campaign_id, $html="", $is_autoresponder =0){
	ini_set('allow_url_fopen',1);

	$upload_path= config_item('user_public');
	$CI =& get_instance();
	$user_dir = $CI->session->userdata('member_id') % 1000;
		
	
	
	if($is_autoresponder !=1){
	$campaign_type_url = 'c';
	$campaign_folder = 'email_templates';
	}else{
	$campaign_type_url = 'a';
	$campaign_folder = 'autoresponders';
	}
	$html_strp=str_get_html($html);	
		
	#########################################
	# 		Set header image height			#
	#########################################
	foreach($html_strp->find('img.header_img') as $e){
		$header_img=$e->src;
				
		list($width_orig, $height_orig,$type_header) = getimagesize(str_replace(base_url().'webappassets/',config_item('webappassets_path'),$header_img));
		$ratio_orig = $width_orig/$height_orig;	  
		$header_height=substr($header_height,0,-2);
		$width=595;
		$height=$width/$ratio_orig;
		//$header_height=intval($height);
		$e->height=$header_height;
	}
	$logo_display=false;
	foreach($html_strp->find('div#logo') as $e){
		$logo_style= $e->getAttribute('style')  ;
		$logo_display=true;
	}
	
	if($logo_display){
		######################################
		# Merge Logo image and header image	 #
		######################################
		$logo_style_arr=explode(";",$logo_style);
		foreach($logo_style_arr as $logos){
			$logo_att_arr=explode(":",$logos);		
			$logo_att_arr[0]=trim($logo_att_arr[0]);
			if($logo_att_arr[0]=="left"){
				$logo_x=substr($logo_att_arr[1],0,-2);
			}
			if($logo_att_arr[0]=="top"){
				$logo_y=substr($logo_att_arr[1],0,-2);
			}			
		}
		$logo_img="";
		foreach($html_strp->find('img#logo_img_id') as $e){
			$logo_img= $e->src  ;
			
			$logo_img_width= $e->getAttribute('style') ;
		}
		foreach($html_strp->find('img.header_img') as $e){
			$header_img= $e->src  ;
			$header_style= $e->getAttribute('style') ;
		}
		$logo_width_arr=explode(";",$logo_img_width);
		foreach($logo_width_arr as $logos){
			$logo_att_arr=explode(":",$logos);
			$logo_att_arr[0]=trim($logo_att_arr[0]);
			if($logo_att_arr[0]=="width"){
				$logo_width=$logo_att_arr[1];
			}if($logo_att_arr[0]=="height"){
				$logo_height=$logo_att_arr[1];
			}
		}
		
		
		#$upload_path= 'c:/xampp/htdocs/rcdata/user/public/';
		$header_path_parts=pathinfo($header_img);
		$backgroundSource = $header_img;
		$feedBurnerStatsSource = $logo_img;	
		 
		
		
		// The file
		$filename = str_replace(base_url().'asset/user_files/',$upload_path.$user_dir.'/', $logo_img);
		$filename = preg_replace('/\?.*/', '', $filename);
		
		

		// Set a maximum height and width
		$width = substr($logo_width,0,-2);
		$height = substr($logo_height,0,-2);
		
		/**
		*	Logo Image is recreated with new dimension which is set in DIY.
		*	New Name is like campaign_id_logo.ext		
		*/
		/* Actual height found to get the ratio and accordingly new dimension calculated */
				
		list($width_orig, $height_orig,$type) = getimagesize($filename);
		
		$ratio_orig = $width_orig/$height_orig;
		if($height==""){
			$height = $width/$ratio_orig;
		}
		$width_logo=$width;
		$height_logo=$height;
		// Resample
		//$image_p = imagecreatetruecolor($width, $height);
		if($type==1){		
			$feedBurnerStatsSource =$upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id."_logo.gif";
		}else if($type==2){
			$feedBurnerStatsSource =$upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id."_logo.jpg";
		}else if($type==3){
			$feedBurnerStatsSource =$upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id."_logo.png";			 
		}
		
		resize($filename, $width_logo, $height_logo, $feedBurnerStatsSource);
		
		
		// Output 
		if($type==1){
			$feedBurnerStats = imagecreatefromgif($feedBurnerStatsSource);
		}else if($type==2){
			$feedBurnerStats = imagecreatefromjpeg($feedBurnerStatsSource);
		}else if($type==3){
			$feedBurnerStats = imagecreatefrompng($feedBurnerStatsSource);			
		}
		
		$feedBurnerStatsX = imagesx($feedBurnerStats);
		$feedBurnerStatsY = imagesy($feedBurnerStats);
		
		//imagedestroy($image_p);
		/**
		*	Header Image is recreated with new dimension which is set in DIY.
		*	New Name is like campaign_id_header.ext		
		*/
		/* Actual height found to get the ratio and accordingly new dimension calculated */
	
		
		$filename= str_replace(base_url().'webappassets/',config_item('webappassets_path'),$header_img);
		
		list($width_orig, $height_orig,$type_header) = getimagesize($filename);
		$ratio_orig = $width_orig/$height_orig;
		$width=595;
		$height=intval($width/$ratio_orig);
		if($type_header==1){
			$backgroundSource = $upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id."_header.gif"; 
		}else if($type_header==2){
			$backgroundSource = $upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id."_header.jpg"; 
		}else if($type_header==3){
			$backgroundSource = $upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id."_header.png"; 
		}
		resize($filename, $width, $height, $backgroundSource);
		// Output 
		if($type_header==1){
			$outputImage = imagecreatefromgif($backgroundSource);
		}else if($type_header==2){
			$outputImage = imagecreatefromjpeg($backgroundSource);
		}else if($type_header==3){
			$outputImage = imagecreatefrompng($backgroundSource);
			imagealphablending($outputImage, true); /* <- must be set to retain alpha blending/merging */
			imagesavealpha($outputImage, true);
		}
		/**
		*	Merge Images
		*/
		 
		imagecopymerge_alpha($outputImage,$feedBurnerStats,$logo_x,$logo_y,0,0,$width_logo, $height_logo,100);
		 
		if($type_header==1){
			imagegif($outputImage,$upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id.'.gif');
			$header_img=base_url().'asset/user_files/'.$campaign_type_url.'/'.$campaign_id.'.gif';
		}else if($type_header==2){
			imagejpeg($outputImage,$upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id.'.jpg');
			$header_img=base_url().'asset/user_files/'.$campaign_type_url.'/'.$campaign_id.'.jpg';
		}else if($type_header==3){
			imagepng($outputImage,$upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/'.$campaign_folder.'/'.$campaign_id.'/'.$campaign_id.'.png');
			$header_img=base_url().'asset/user_files/'.$campaign_type_url.'/'.$campaign_id.'.png';
		}
		foreach($html_strp->find('img.header_img') as $e)
		$e->src=$header_img;	
	}
	foreach($html_strp->find('div.header_div') as $header_div){
		foreach($header_div->find('img.header_link_show') as $header_img_link){
			$header_link=$header_img_link->name;
		}
		$header_div->outertext = "";
	}
	foreach($html_strp->find('div.text-paragraph-container') as $e){		
		foreach($e->find('p') as $p){			
			$p_style=$p->getAttribute("style");
			$patterns = '/padding(.*?);/';
			$replacements = '';
			$p_style= preg_replace($patterns, $replacements, $p_style);
			$p->setAttribute("style","margin:0px;margin-bottom:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;".$p_style);
		}
		foreach($e->find('table') as $table){
			$table_style=$table->getAttribute("style");
			$patterns = '/padding(.*?);/';
			$replacements = '';
			$table_style= preg_replace($patterns, $replacements, $table_style);
			$table->setAttribute("style","margin:0px;margin-bottom:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;".$table_style);
		}
		for($i=1;$i<=6;$i++){
			foreach($e->find("h$i") as $h){
				$h_style=$h->getAttribute("style");
				$patterns = '/padding(.*?);/';
				$replacements = '';
				$h_style= preg_replace($patterns, $replacements, $h_style);
				$h->setAttribute("style","margin:0px;margin-bottom:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;".$h_style);
			}
		}
	
		foreach($e->find('li') as $li){
			$li_style=$li->getAttribute("style");
			//$li_style=eregi_replace(';$', '', $li_style).";list-style-position: inside;";
			$li_style=preg_replace('/;$/', '', $li_style).";list-style-position: inside;";
			$li->setAttribute("style","$li_style");
		}
		$e->removeAttribute('contenteditable');
		$style=	$e->getAttribute('style');
		if($e->getAttribute('class') == 'header-text text-paragraph-container')
		$style.=";border:2px solid transparent;font-family:Arial,Helvetica,sans-serif;color:#333333;font-size:18px";
		else
		$style.=";border:2px solid transparent;font-family:Arial,Helvetica,sans-serif;color:#333333;font-size:14px";
		$e->setAttribute("style","$style");
		$e->setAttribute("valign","top");
	}

	
	foreach($html_strp->find('div.edit_offer') as $e){
		foreach($e->find('p') as $p){
			$style=	strtolower ($p->getAttribute('style'));
			if($style!=""){
				$padding_position=strpos($style,"padding");
				if($padding_position===false){
					$p->setAttribute("style","margin:0px;margin-bottom:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;".$style);
				}
			}else{
				$p->setAttribute("style","margin:0px;margin-bottom:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;");
			}
		}		
	}
	foreach($html_strp->find('div.iconWrap') as $e)
	$e->outertext = "";
	foreach($html_strp->find('div.handler_img') as $e)
	$e->outertext = ""; 
	foreach($html_strp->find('div.resize_div') as $e)
	$e->outertext = "";
	foreach($html_strp->find('div.resize_div_text') as $e)
	$e->outertext = ""; 
	foreach($html_strp->find('tr.handler_tr') as $e)
	$e->outertext = "";
	foreach($html_strp->find('ul.handler') as $e)
	$e->outertext = "";
	foreach($html_strp->find('div.ui-sortable-helper') as $e)
	$e->outertext = "";
	foreach($html_strp->find('div.ui-draggable') as $e)
	$e->outertext = "";
	foreach($html_strp->find('div.ui-state-highlight') as $e)
	$e->outertext = "";
	foreach($html_strp->find('div.handler_img_width_height') as $e)
	$e->outertext = "";
	foreach($html_strp->find('div.video_play') as $e)
	$e->outertext = "";
	foreach($html_strp->find('table#email_template_table') as $e)
	$e->setAttribute("align","center");
	//Create social media Link
	foreach($html_strp->find('a.social_media_link') as $a){
		$a->setAttribute("href",$a->name);
	}
	
	foreach($html_strp->find('table#email_template_table') as $table){
		if($table->width=="100%"){
			$table->setAttribute("width",'599');
		}
	}
	// Youtube Video Link
	foreach($html_strp->find('table.youtube_container') as $tbl_html){
		$thisVideoId=$tbl_html->id;
		$img_link=$tbl_html->name;
		foreach($tbl_html->find('.youtube_image_caption') as $e){
			$youtube_image_caption = $e->innertext;
		}
		if($img_link!=""){
		
		// get video url
				foreach($tbl_html->find('img.image-container') as $e){
					$video_img_name = $e->name;
					$video_img_url 	= $e->src;
					$video_width 	= $e->width;
					$video_height 	= $e->height;
				
					if(!file_exists($upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/video_img')){
						mkdir($upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/video_img',0777);
						chmod($upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/video_img',0777);					
					}
					// resize image and merge with play button	
					$newVideoImg = $upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/video_img/'.$CI->session->userdata('member_id').'_'.$campaign_id.'_'.$thisVideoId.'.jpg';				
					resize($video_img_url, $video_width, $video_height, $newVideoImg);
					$local_img 	= imagecreatefromjpeg($newVideoImg);		
					
					imagecopymerge_alpha($local_img,imagecreatefrompng(config_item('webappassets_path').'images/play.png'),($video_width-58)/2,($video_height-41)/2,0,0,58,41,100);
					
					imagejpeg($local_img,$upload_path.$user_dir.'/'.$CI->session->userdata('member_id').'/video_img/'.$CI->session->userdata('member_id').'_'.$campaign_id.'_'.$thisVideoId.'.jpg');
					$video_img=base_url().'asset/user_files/video_img/'.$CI->session->userdata('member_id').'_'.$campaign_id.'_'.$thisVideoId.'.jpg';
				
					$e->src=$video_img;					
				}
				foreach($tbl_html->find('div.video_play') as $ve)
				$ve->setAttribute("style",'');				
				
				
			foreach($tbl_html->find('div.position_div') as $div){
				
				$img=$div->innertext;
				if (false === strpos($img_link, '://')) {
					$img_link = 'http://' . $img_link;
				}
				$div->innertext = "<a href='".$img_link."' border='0' target='_blank'>".$img."</a><font class='youtube_image_caption' style='font-size:11px;display:block;text-align:left;'>$youtube_image_caption</font>";
			}
		}
		//$img_l->outertext = "";
	}
	// Image Link
	foreach($html_strp->find('td.img_content') as $td_html){
		foreach($td_html->find('img.image_link') as $img_l){
			$img_link=$img_l->name;
			if($img_link!=""){
				foreach($td_html->find('div.position_div') as $div){
					$img=$div->innertext;
					$div->innertext="<a href='".$img_link."' border='0' target='_blank'>".$img."</a>";
				}
			}
			$img_l->outertext = "";
		}		
	}
	foreach($html_strp->find('.divider_block') as $td_divider){
		$td_divider->setAttribute("style",'padding-top:5px;padding-bottom:5px;');
	}
	//HEader Link
	if($header_link!=""){
		foreach($html_strp->find('td#header') as $td_html){
			$header_html=$td_html->innertext;
			$td_html->innertext="<a href='".$header_link."' border='0' target='_blank'>".$header_html."</a>";
		}
	}
	foreach($html_strp->find('td#footer') as $td_html){
		$td_html->setAttribute("align",'left');
	}
	foreach($html_strp->find('span.copyright') as $e)
	$e->innertext="&copy; ";
	//Get email body background color
	foreach($html_strp->find('td#body_main') as $e){
		$style=$e->getAttribute('style');
		$style_arr=explode(":",$style);
	//	$background_color= trim('#ffffff');
	 
		$background_color= trim($style_arr[1]);
		$e->setAttribute("style",$style.';color:#333333;'); 
	}
	foreach($html_strp->find('table.resize_table') as $e){
		$style=$e->getAttribute('style');
		$e->setAttribute("style",$style.'border:none;border-spacing:0px;'); 
	}
	foreach($html_strp->find('td.social_li') as $e){
		$style=$e->getAttribute('style');
		$e->setAttribute("style",$style.'padding-left:3px;padding-right: 3px;padding-top:0px;padding-bottom:0px;'); 
	}
	foreach($html_strp->find('hr') as $e){		
		$e->setAttribute("style",'width:547px;margin-top:0;margin-bottom:0;padding-left:3px;padding-right: 3px;padding-top:0px;padding-bottom:0px;'); 
	}
	$i=1;
	foreach($html_strp->find('table.container-div') as $e){
		$style="  border:none;clear:both; ";
		
		if('container-div preheader'==$e->getAttribute("class")){
			$i = 0;
			/*
			foreach($html_strp->find('div.empty_preheader') as $e) {
				if(trim($e->innertext) == 'This is a pre-header. Here you can write a short preview of your email content.')
				$e->outertext = '';
			}
			*/
		}else{		
			$style .=" width: 100%; border-top-color:".$background_color.";border-bottom-color:".$background_color."; border-top-style: solid;  border-bottom-style: solid;";
			$style .="border-left-color:".$background_color."; border-left-style: solid;border-left-width: 20px;border-right-color:".$background_color."; border-right-style: solid;border-right-width: 20px;";		
			if($i==1){
				if($i==count($html_strp->find('table.container-div'))){
					$style .="border-top-width: 20px; border-bottom-width: 20px;";									
				}else{
					$style .="border-top-width: 20px; border-bottom-width: 7px;";					
				}		
			}else{
				if($i==count($html_strp->find('table.container-div'))){					
					$style .="border-top-width: 8px;  border-bottom-width: 20px;";
				}else{					
					$style .="border-top-width: 8px; border-bottom-width: 7px;";	
				}		
			}
		}
		
		$e->setAttribute("style",$style);
		$e->setAttribute("cellspacing","0");
		$e->setAttribute("cellpadding","0");
		$i++;
	}
	
	foreach($html_strp->find('div') as $e){
		$e->removeAttribute('class');
		$e->removeAttribute('tabindex');
		$e->removeAttribute('valign');
	}	
	$text =  $html_strp->save();
	$html_strp->clear();
	unset($html_strp);
	return fix_html_err($text); 
}
/**
 * Helper to fix html errors
 * Using htmlfixer.class.php
 */
 
function fix_html_err($dirty_html = ''){
$dom = new DOMDocument();
$dom->preserveWhiteSpace = FALSE;
libxml_use_internal_errors(true);
@$dom->loadHTML("$dirty_html");
libxml_clear_errors();
$dom->formatOutput = TRUE;

// remove attributes "id" and "name"
$xpath = new DOMXPath($dom);            // create a new XPath
$nodes = $xpath->query('//*[@id]');  // Find elements with a style attribute
foreach ($nodes as $node) {              // Iterate over found elements
    $node->removeAttribute('id');    // Remove style attribute
}
$nodes2 = $xpath->query('//*[@name]');  // Find elements with a style attribute
foreach ($nodes2 as $node) {              // Iterate over found elements
    $node->removeAttribute('name');    // Remove style attribute
}
// remove attributes "id" and "name" ends
	
$clean_html = $dom->saveXML();

$format = new Format;

return $format->HTML($clean_html);


/* require_once '../libraries/HTMLPurifier.auto.php';

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
return $purifier->purify($dirty_html);
	 */
	//$a = new HtmlFixer();
	//return $a->getFixedHtml($dirty_html);

}



function parse_html($html=""){
		   
	$html_strp=str_get_html($html);
	$text =  $html_strp->save();
	$html_strp->clear();
	unset($html_strp);
	return $text;
}
/**
* This function adds html element wrapper within body tag of html
*/
function wrap_element_around_element_in_html($html="",$prefix="",$suffix=""){	
	$html = str_get_html($html, false);
	//$html->find('head',0)->innertext =	$html->find('head',0)->innertext.		'<!--[if gte mso 9]><style>ol li {list-style-type: square !important;}ul { vertical-align:middle;}li{ line-height:1.5 !important;  margin-bottom:10px;}hr{ margin-bottom:10px;}p {line-height: 1.5 !important; mso-text-raise:0; mso-line-height-rule:exactly;  -mso-line-height-rule:exactly; margin-bottom:10px;}</style><![en=dif]-->';

	$html->find('body',0)->innertext =	$prefix.$html->find('body',0)->innertext.$suffix;
	
	$html->find('body',0)->removeAttribute('style');
	$html->find('body',0)->removeAttribute('class');
	$html->find('html',0)->removeAttribute('style');
	
	
	foreach($html->find('img') as $e){
		$e->src = str_replace (array("\r\n", "\n", "\r"), '', $e->src);
		$e->src = preg_replace('/\s/', '', $e->src);
	}
	
return $html;	
}
/**
  * This function removes top-link from the browser view.
  * only preheader remains
  */
function replaceTopLinks($html="",$newLinks){
	$html_strp=str_get_html($html);
	foreach($html_strp->find('td.mailTopLink') as $e) {
		$e->innertext = $newLinks;
	}
 
	$ret_html =  $html_strp->save();
	unset($html_strp);
	
	 return $ret_html;
}
/**
  * This function removes top-link from the browser view.
  * only preheader remains
  */
function removeDefaultPreheader($html=""){
	$html_strp=str_get_html($html);
	foreach($html_strp->find('td.td_preheader div') as $e) {
		if(trim($e->innertext) == 'This is a pre-header. Here you can write a short preview of your email content.' or trim($e->innertext) == 'This is a pre-header. Use this area to write a short preview of your email content.'){
			foreach($html_strp->find('td.td_preheader') as $td) 
			$td->outertext = '';			
			foreach($html_strp->find('td.mailTopLink table td') as $lnk) {				
				$lnk->setAttribute("align","center");
			}
			
			foreach($html_strp->find('td.mailTopLink div') as $lnk) {
				$style	= $lnk->getAttribute('style');
				$lnk->setAttribute("style",str_replace('right','center',$style));
			}
		}else{
			foreach($html_strp->find('td.mailTopLink table td') as $lnk) {				
				$lnk->setAttribute("align","right");
			}
		}
	}	
	$ret_html =  $html_strp->save();
	unset($html_strp);
	
	 return $ret_html;
}
function removeHTMLElement($html="",$identifier='td.mailTopLink'){
	$html_strp=str_get_html($html);
	foreach($html_strp->find($identifier) as $e) {
		$e->outertext = '';
	}
	
	$ret_html =  $html_strp->save();
	unset($html_strp);
	
	 return $ret_html;
}
function change_link($html="",$campaign_id=0,$autoresponder=false){
	
	$html_strp=str_get_html($html);
	$url_array=array();
	$i=1;
	foreach($html_strp->find('a') as $a) {
		if($a->href) {
			if (strtolower(substr($a->href, 0, 7)) != 'mailto:' && strtolower(substr($a->href, 0, 6)) != 'skype:') {
				$original_url= trim($a->href);
				$encode_url= base64UrlEncode($original_url);
				
				if(!(in_array($encode_url,$url_array))){
					$url_array[$i]=$encode_url;
					$url_key=$i;
					$i++;
				}else{
					$url_key = array_search($encode_url, $url_array);
				}
				if($autoresponder){
					$url=base_url().'newsletter/clickrate/create_autoresponder/'.$campaign_id.'/[scheduled_id]/[subscriber_id]/'.$url_key;
				}else{
					$url=base_url().'newsletter/clickrate/create/'.$campaign_id.'/[subscriber_id]/'.$url_key;
				}
				$a->href= trim($url);
			}
		}
	}
	 $text =  $html_strp->save();
	 unset($html_strp);
	 $serialize_array=serialize($url_array);
	 $return_array=array(0=>$text, 1=>$serialize_array );
	 return $return_array;
}
/**
*	Function change_footer to update company info on footer
**/
function change_footer($company='',$address_line_1='',$city='',$state='',$zipcode='',$country='',$campaign_content='',$campaign_email_content=''){
		   
	$html_strp=str_get_html($campaign_content);
	foreach($html_strp->find('.company_name') as $company_html){
		$company_html->innertext="<b><span class='copyright'>&copy; </span>".$company."</b>";
	}
	foreach($html_strp->find('.address') as $address_html){
		$address_html->innertext=$address_line_1;
	}
	foreach($html_strp->find('.city') as $city_html){
		$city_html->innertext=' | '.$city;
	}
	foreach($html_strp->find('.state') as $state_html){
		$state_html->innertext=', '.$state;
	}
	foreach($html_strp->find('.zip') as $zip_html){
		$zip_html->innertext=' | '.$zipcode;
	}
	foreach($html_strp->find('.country') as $country_html){
		if('United States' != $country and 'USA' != $country)
		$country_html->innertext=' | '.$country;
		else
		$country_html->innertext='';
	}
	$campaign_content =  $html_strp->save();
	unset($html_strp);
	$campaign_email_content=htmlspecialchars_decode(html_entity_decode($campaign_email_content, ENT_QUOTES, "utf-8" ));
	
	$html_strp=str_get_html($campaign_email_content);
	foreach($html_strp->find('.company_name') as $company_html){
		$company_html->innertext="<b><span class='copyright'>&copy; </span>".$company."</b>";
	}
	foreach($html_strp->find('.address') as $address_html){
		$address_html->innertext=$address_line_1;
	}
	foreach($html_strp->find('.city') as $city_html){
		$city_html->innertext=' | '.$city;
	}
	foreach($html_strp->find('.state') as $state_html){
		$state_html->innertext=', '.$state;
	}
	foreach($html_strp->find('.zip') as $zip_html){
		$zip_html->innertext=' '.$zipcode;
	}
	foreach($html_strp->find('.country') as $country_html){
		$country_html->innertext=' '.$country;
	}
	$campaign_email_content =  $html_strp->save();
	$campaign_email_content =  htmlspecialchars($campaign_email_content);
	
	unset($html_strp);
	$campaign=$campaign_content."xxx_campaign_content_xxx".$campaign_email_content;
	
	return $campaign;
}
function base64UrlEncode($data)
{
	$data=trim($data);
	if($data)
	{
		return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
	}else{
		return "";
	}
}
/**
 * merge two true colour images while maintaining alpha transparency of both
 * images.
 *
 * known issues : Opacity values other than 100% get a bit screwy, the source
 *                composition determines how much this issue will annoy you.
 *                if in doubt, use as you would imagecopy_alpha (i.e. keep
 *                opacity at 100%)
 *
 * @access public
 *
 * @param  resource $dst  Destination image link resource
 * @param  resource $src  Source image link resource
 * @param  int      $dstX x-coordinate of destination point
 * @param  int      $dstY y-coordinate of destination point
 * @param  int      $srcX x-coordinate of source point
 * @param  int      $srcY y-coordinate of source point
 * @param  int      $w    Source width
 * @param  int      $h    Source height
 * @param  int      $pct  Opacity or source image
 ******************************************************************************/
function imagecopymerge_alpha($dst, $src, $dstX, $dstY, $srcX, $srcY, $w, $h, $pct)
{
    $pct /= 100;
 
    /* make sure opacity level is within range before going any further */
    $pct  = max(min(1, $pct), 0);
 
    if ($pct == 0)
    {
        /* 0% opacity? then we have nothing to do */
        return;
    }
 
    /* work out if we need to bother correcting for opacity */
    if ($pct < 1)
    {
        /* we need a copy of the original to work from, only copy the cropped */
        /* area of src                                                        */
        $srccopy  = imagecreatetruecolor($w, $h);
 
        /* attempt to maintain alpha levels, alpha blending must be *off* */
        imagealphablending($srccopy, false);
        imagesavealpha($srccopy, true);
 
        imagecopy($srccopy, $src, 0, 0, $srcX, $srcY, $w, $h);
 
        /* we need to know the max transaprency of the image */
        $max_t = 0;
 
        for ($y = 0; $y < $h; $y++)
        {
            for ($x = 0; $x < $w; $x++)
            {
                $src_c = imagecolorat($srccopy, $x, $y);
                $src_a = ($src_c >> 24) & 0xFF;
 
                $max_t = $src_a > $max_t ? $src_a : $max_t;
            }
        }
        /* src has no transparency? set it to use full alpha range */
        $max_t = $max_t == 0 ? 127 : $max_t;
 
        /* $max_t is now being reused as the correction factor to apply based */
        /* on the original transparency range of  src                         */
        $max_t /= 127;
 
        /* go back through the image adjusting alpha channel as required */
        for ($y = 0; $y < $h; $y++)
        {
            for ($x = 0; $x < $w; $x++)
            {
                $src_c  = imagecolorat($src, $srcX + $x, $srcY + $y);
                $src_a  = ($src_c >> 24) & 0xFF;
                $src_r  = ($src_c >> 16) & 0xFF;
                $src_g  = ($src_c >>  8) & 0xFF;
                $src_b  = ($src_c)       & 0xFF;
 
                /* alpha channel compensation */
                $src_a = ($src_a + 127 - (127 * $pct)) * $max_t;
                $src_a = ($src_a > 127) ? 127 : (int)$src_a;
 
                /* get and set this pixel's adjusted RGBA colour index */
                $rgba  = ImageColorAllocateAlpha($srccopy, $src_r, $src_g, $src_b, $src_a);
 
                /* ImageColorAllocateAlpha returns -1 for PHP versions prior  */
                /* to 5.1.3 when allocation failed                               */
                if ($rgba === false || $rgba == -1)
                {
                    $rgba = ImageColorClosestAlpha($srccopy, $src_r, $src_g, $src_b, $src_a);
                }
 
                imagesetpixel($srccopy, $x, $y, $rgba);
            }
        }
 
        /* call imagecopy passing our alpha adjusted image as src */
        imagecopy($dst, $srccopy, $dstX, $dstY, 0, 0, $w, $h);
 
        /* cleanup, free memory */
        imagedestroy($srccopy);
        return;
    }
 
    /* still here? no opacity adjustment required so pass straight through to */
    /* imagecopy rather than imagecopymerge to retain alpha channels          */
    imagecopy($dst, $src, $dstX, $dstY, $srcX, $srcY, $w, $h);
    return;
}
 
function resize($img, $w, $h, $newfilename) {
 
 //Check if GD extension is loaded
 if (!extension_loaded('gd') && !extension_loaded('gd2')) {
  trigger_error("GD is not loaded", E_USER_WARNING);
  return false;
 }
 
 //Get Image size info
 $imgInfo = getimagesize($img);
 switch ($imgInfo[2]) {
  case 1: $im = imagecreatefromgif($img); break;
  case 2: $im = imagecreatefromjpeg($img);  break;
  case 3: $im = imagecreatefrompng($img); break;
  default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
 }
 
 //If image dimension is smaller, do not resize
 if ($imgInfo[0] <= $w && $imgInfo[1] <= $h) {
  /* $nHeight = $imgInfo[1];
  $nWidth = $imgInfo[0];
  // updated on 21st march 2013 for youtube video play icon issue
   */
  $nHeight = $h;
  $nWidth = $w;
 }else{
  //yeah, resize it, but keep it proportional
  if ($w/$imgInfo[0] > $h/$imgInfo[1]) {
   $nWidth = $w;
   $nHeight = $imgInfo[1]*($w/$imgInfo[0]);
  }else{
   $nWidth = $imgInfo[0]*($h/$imgInfo[1]);
   $nHeight = $h;
  }
 }
 $nWidth = round($nWidth);
 $nHeight = round($nHeight);
 
 $newImg = imagecreatetruecolor($nWidth, $nHeight);
 
 /* Check if this image is PNG or GIF, then set if Transparent*/  
 if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
  imagealphablending($newImg, false);
  imagesavealpha($newImg,true);
  $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
  imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
 }
 imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
 
 //Generate the file, and rename it to $newfilename
 switch ($imgInfo[2]) {
  case 1: imagegif($newImg,$newfilename); break;
  case 2: imagejpeg($newImg,$newfilename);  break;
  case 3: imagepng($newImg,$newfilename); break;
  default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
 }
   
   return $newfilename;
}
?>