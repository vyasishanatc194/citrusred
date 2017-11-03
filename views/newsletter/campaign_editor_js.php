<?php
 load_class('JSMin','libraries', '');

$arrJSFiles = array(
			'js/jquery-1.5.1.min.js',
			'js/jquery-ui-1.8.13.custom.min.js',
			'js/jquery.cookie.js',
			'js/nicEdit.js',
			'js/colortip-1.0-jquery.js', 
			'js/fancybox/jquery.fancybox-1.3.4.pack.js',
			'js/fancybox/jquery.mousewheel-3.0.4.pack.js',
			'js/jquery.upload-1.0.2.js',
			'js/colorpicker.js',
			'js/jquery.blockUI.js',
			'js/jquery.masonry.min.js'
			);
			//'js/jquery.qtip.min.js?v=6-20-13',
$modified_time = 0;
foreach($arrJSFiles as $jsfile){
	$js_output .= (file_get_contents(FCFOLDER . '/webappassets/'.$jsfile))."\n\n\n";
	$modified_time = max(filemtime(FCFOLDER . '/webappassets/'.$jsfile), $modified_time);
}
// $js_output = JSMin::minify($js_output);

Header("content-type: application/x-javascript");
echo ($js_output);
?>
/***
	Define Variables
***/
/* window.onerror = function(msg, url, linenumber) {
    alert('Error message: '+msg+'\nURL: '+url+'\nLine Number: '+linenumber);
    return true;
} */

var pagechange=false;		//set Page content change
var border_style=0;			//Check border style none or not none
var body_blocks=new Array('body_main');	// Define blocks

var block_text_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div' id='[text_block_id]'><tr><td align='center' class='handler_div' ><div  class='text-paragraph-container' style='padding:0 15px;text-align:left;line-height:1.7;' ><div class='empty_text'>This is a text block. Click here to add text.</div></div></td></tr></table>";		//Text block content

var block_title_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div' id='[title_block_id]'><tr><td align='center'  class='handler_div'><div class='header-text text-paragraph-container' style='padding:0 15px; line-height:1.7; text-align: left; font-size: 18px; font-weight: bold;'><div class='empty_title'>This is a title block. Click here to add text.</div></div></td></tr></table>";	//Title block content

var block_image_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div' id='[image_block_id]'><tr><td  class='handler_div' align='center'><table  align='center' class='resize_table' cellpadding='0' cellspacing='0'><tr><td align='left' class='img_content' id='[image1_block_id]' valign='top'><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image' width='265' height='265'  name='1' border='0'/><div class='resize_div ' style='width: 265px; height: 265px;'><div  class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td></tr></table></td></tr></table>";	//Image block content

var block_image2_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div' id='[image_block_id]'><tr><td  class='handler_div'><table  align='center' class='resize_table' cellspacing='0' cellpadding='0'><tr><td align='left' class='img_content' id='[image1_block_id]' valign='top' ><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image' width='265' height='265' name='1' border='0' /><div class='resize_div ' style='width: 265px; height: 265px; '><div  class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td><td align='left' class='img_content' id='[image2_block_id]' valign='top' ><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image' border='0'  width='265' height='265'  name='1' /><div class='resize_div ' style='width: 265px; height: 265px;'><div class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td></tr></table></td></tr></table>";	//2 Images in image group

var block_image3_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div' id='[image_block_id]'><tr><td  class='handler_div'><table  align='center' class='resize_table' cellspacing='0' cellpadding='0'><tr><td align='left' class='img_content' id='[image1_block_id]'  valign='top'><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image'  width='168' height='168' border='0' name='1' /><div class='resize_div ' style='width: 168px; height: 168px; '><div class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td><td align='left' class='img_content' id='[image2_block_id]'  valign='top'><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image' border='0' width='168' height='168' name='1' /><div class='resize_div ' style=' width: 168px; height: 168px;'><div class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td><td align='left' id='[image3_block_id]' class='img_content'   valign='top'><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image' border='0' width='168' height='168' name='1' /><div class='resize_div ' style='width: 168px; height: 168px;'><div  class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td></tr></table></td></tr></table>";	//3 Images in image group

var block_image4_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div' id='[image_block_id]'><tr><td  class='handler_div'><table  align='center' class='resize_table' cellspacing='0' cellpadding='0'><tr><td align='left' id='[image1_block_id]' class='img_content' valign='top' ><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' width='120' height='120' class='image-container drop-image' border='0'  name='1'/><div class='resize_div ' style='width: 120px; height: 120px;'><div class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td><td align='left' id='[image2_block_id]' class='img_content'  valign='top' ><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image' border='0' width='120' height='120'  name='1'/><div class='resize_div ' style='width: 120px; height: 120px; '><div  class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td><td align='left' id='[image3_block_id]' class='img_content'  valign='top' ><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image' border='0' width='120' height='120' name='1'/><div class='resize_div ' style=' width: 120px; height: 120px; left: 0px; top: 0px;'><div  class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td><td align='left' id='[image4_block_id]' class='img_content'  valign='top' ><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image' border='0' width='120' height='120' name='1'/><div class='resize_div ' style='width: 120px; height: 120px;'><div class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td></tr></table></td></tr></table>";	//4 Images in image group

var block_image_with_text_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div'  id='[image_text_block_id]' align='center'><tr><td class='handler_div' align='center'><div class='text_img_outer_div' style='padding:0 15px;text-align:left;line-height:1.7;'><table  cellspacing='0' cellpadding='0' class='resize_table' style='width:225px;margin-top:7px;' align='left'><tr><td id='[image_block_id]' class='img_content text_img_content' align='left'><div class='position_div'><img src='<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13' class='image-container drop-image text_image ' border='0' name='1'/><div class='resize_div_text' style='width:200px;height: 200px; '><div class='div_border'><span style='display:none;' class='drop_div_border'></span></div><div  class='highlight_on_image_hover'><span style='display:none;' class='drop_div_border'></span></div></div></div><div class=\"active_image_option\"><span class='img_link_span'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/></span><span class='image_caption'></span></div></td></tr></table><div class='text-paragraph-container' align='left' style='width:100%;min-height:207px;line-height:1.7;'><div class='empty_text'>This is a text block. Click here to add text.</div></div></div></td></tr></table>";


var block_divider_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div divider_block' id='[divider_block_id]' ><tr><td  class='handler_div' align='center'><hr style='width:547px;margin:5px 0;padding:0;' /></td></tr></table>";		//Divider block content

var block_offer_content="<table width='100%' cellspacing='5' style='padding:0px !important;' cellpadding='0' class='container-div offer_block' align='center' id='[offer_block_id]'><tr><td align='center'  class='handler_div'><table width='100%' cellspacing='0' cellpadding='0' class='offer' style='padding-top:10px;padding-bottom:10px;' ><tr><td align='center'><div class='edit_offer_div'  style='border-width:6px;border-style: dashed;border-color:#333333 !important;width:40%;'><div class='edit_offer' style='padding:0px;line-height: 1.7;text-align: center;'><br><font size='6'><b>FREE!</b></font><br /><br />Write offer detail here <br><br><font size='2'>Expires:month/day/year</font><br><br></div></div></td></tr></table></td></tr></table>";//offer block content


var social_media_link_open_tag="<table width='100%' cellspacing='0' cellpadding='0' class='container-div ' align='center' valign='middle' id='[social_block_id]'><tr><td align='center'  class='handler_div'><table  cellspacing='5' cellpadding='0' valign='top'><tr>";		// Social media open tag

var block_social_facebook_content="<td class='social_li' valign='top'><a target='_blank' title='[facebook_link]' class='social_media_link facebook_url_link' name='[facebook_link]'><img src='<?php echo $this->config->item('webappassets');?>images/facebook-share.png?v=6-20-13' alt='facebook' title='[facebook_link]' border='0'/></a> </td>";		//facebook block content

var block_social_twitter_content="<td class='social_li' valign='top'><a target='_blank' title='[twitter_link]' class='social_media_link twitter_url_link' name='[twitter_link]'><img src='<?php echo $this->config->item('webappassets');?>images/twitter-share.png?v=6-20-13' alt='twitter' title='[twitter_link]' border='0'/></a></td> ";		//twitter block content

var block_social_linkedin_content="<td class='social_li' valign='top'><a target='_blank' title='[linkedin_link]' class='social_media_link linkedin_url_link' name='[linkedin_link]'><img src='<?php echo $this->config->item('webappassets');?>images/linkedin-share.png?v=6-20-13' alt='linkedin' title='[linkedin_link]' border='0'/></a></td> ";		//linkedin block content

var block_social_rss_content="<td class='social_li' valign='top'><a target='_blank' title='[rss_link]' class='social_media_link rss_url_link' name='[rss_link]'><img src='<?php echo $this->config->item('webappassets');?>images/rss_icon.png?v=6-20-13' alt='rss' title='[rss_link]' border='0'/></a> </td>";		//rss block content

var block_social_youtube_content="<td class='social_li' valign='top'><a target='_blank' title='[youtube_link]' class='social_media_link youtube_url_link' name='[youtube_link]'><img src='<?php echo $this->config->item('webappassets');?>images/youtube.png?v=6-20-13' alt='youtube' title='[youtube_link]' border='0'/></a></td> ";		//youtube block content

var block_social_google_plus_content="<td class='social_li' valign='top'><a target='_blank' title='[google_plus_link]' class='social_media_link google_plus_url_link' name='[google_plus_link]'><img src='<?php echo $this->config->item('webappassets');?>images/google-plus-share.png?v=6-20-13' alt='google plus' title='[google_plus_link]' border='0'/></a></td> ";		//google plus content

var block_social_tumblr_content="<td class='social_li' valign='top'><a target='_blank' title='[tumblr_link]' class='social_media_link tumblr_url_link' name='[tumblr_link]'><img src='<?php echo $this->config->item('webappassets');?>images/tumblr-share.png?v=6-20-13' alt='tumblr' title='[tumblr_link]' border='0'/></a></td> ";		//tumblr content

var block_social_flickr_content="<td class='social_li' valign='top'><a target='_blank' title='[flickr_link]' class='social_media_link flickr_url_link' name='[flickr_link]'><img src='<?php echo $this->config->item('webappassets');?>images/flickr-share.png?v=6-20-13' alt='flickr' title='[flickr_link]' border='0'/></a></td> ";		//flickr content

var block_social_skype_content="<td class='social_li' valign='top'><a target='_blank' title='[skype_link]' class='social_media_link skype_url_link' name='[skype_link]'><img src='<?php echo $this->config->item('webappassets');?>images/skype-share.png?v=6-20-13' alt='skype' title='[skype_link]' border='0'/></a></td> ";		//skype content

var block_social_pinterest_content="<td class='social_li' valign='top'><a target='_blank' title='[pinterest_link]' class='social_media_link pinterest_url_link' name='[pinterest_link]'><img src='<?php echo $this->config->item('webappassets');?>images/pinterest.png?v=6-20-13' alt='pinterest' title='[pinterest_link]' border='0'/></a></td> ";		//pinterest content

var block_social_instagram_content="<td class='social_li' valign='top'><a target='_blank' title='[instagram_link]' class='social_media_link instagram_url_link' name='[instagram_link]'><img src='<?php echo $this->config->item('webappassets');?>images/instagram.png?v=6-20-13' alt='instagram' title='[instagram_link]' border='0'/></a></td> ";		//instagram content

var block_mailto_content="<td class='social_li' valign='top'><a target='_blank' title='[mailto_link]' class='social_media_link mailto_url_link' name='[mailto_link]'><img src='<?php echo $this->config->item('webappassets');?>images/email-share.png?v=6-20-13' alt='mailto' title='[mailto_link]' border='0'/></a></td> ";		//mailto content

var block_website_content="<td class='social_li' valign='top'><a target='_blank' title='[website_link]' class='social_media_link website_url_link' name='[website_link]'><img src='<?php echo $this->config->item('webappassets');?>images/website-share.png?v=6-20-13' alt='website' title='[website_link]' border='0'/></a></td> ";		//Website content

var social_media_link_close_tag="</tr></table></td></tr></table>";		// Social media close tag

var blok_youtube_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div youtube_container' id='[image_block_id]' name='[youtube_link]'><tr><td  class='handler_div'  align='center'><table style='width:550px;border-spacing:0px;border:none;' cellspacing='0' cellpadding='0' border='0' align='center' class='resize_table'><tr><td align='center' class='img_content' id='[image1_block_id]' valign='top'><div class='position_div' style=\"position:relative;text-align:center;display:inline-block;\"><img src='[youtube_img]' class='image-container drop-image'  width='300'  height='176' border='0' /><div class='video_play' style=\"position: absolute;top: 80px;left: 103px;width: 58px;height: 41px;background-image: url('<?php echo $this->config->item('webappassets');?>images/play.png?v=6-20-13')\"></div><div class='resize_div ' style='position: absolute; text-align: center; width: 300px; height: 0px; left: 0px; top: 0px; z-index: 10; border-color: transparent;'><div style='display: block; position: absolute; top: -1px; bottom: -1px; right: -1px; left: -1px; border: 1px solid rgb(240, 240, 240);' class='div_border'><span style='display:none;' class='drop_div_border'></span></div></div></div><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/><span class='youtube_image_caption'></span></td></tr></table></td></tr></table>";	//Youtube block content

var blok_vimeo_video_content="<table width='100%' cellspacing='0' cellpadding='0' class='container-div youtube_container' id='[image_block_id]' name='[vimeo_video_link]'><tr><td  class='handler_div'><table style='width:550px;' border='0' align='center' class='resize_table'><tr><td align='center' class='img_content' id='[image1_block_id]' valign='top'><div class='position_div' style=\"position:relative;text-align:center;display:inline-block;\"><img src='[vimeo_video_img]' class='image-container drop-image'  width='300' height='176' border='0' /><div class='video_play' style=\"position: absolute;top: 80px;left: 103px;width: 58px;height: 41px;background-image: url('<?php echo $this->config->item('webappassets');?>images/play.png?v=6-20-13')\"></div><div class='resize_div ' style='position: absolute; text-align: center; width: 300px; height: 0px; left: 0px; top: 0px; z-index: 10; border-color: transparent;'><div style='display: block; position: absolute; top: -1px; bottom: -1px; right: -1px; left: -1px; border: 1px solid rgb(240, 240, 240);' class='div_border'><span style='display:none;' class='drop_div_border'></span></div></div></div><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' class='image_link' style='display:none;'/><span class='youtube_image_caption'></span></td></tr></table></td></tr></table>";	//Youtube block content

var handler='<div class="handler_ul_div"><ul style="list-style: none outside none;" class="handler"><li class="alignright"><a class="close-link" ><img src="<?php echo base_url() ?>webappassets/images/cross-script.png?v=6-20-13" title="Delete" /></a></li><li class="drag-center" ><a class="drag_handler"><img src="<?php echo base_url() ?>webappassets/images/drag.png?v=6-20-13" title="Drag"/></a></li></ul></div>';		//Blocks handler


var handler_image1="<div class='handler_img '><ul><li style='width:33%'><a  class='option_image-link'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' title='link'/></a></li><li style='width:33%'><a  class='option_image-caption' ><img src='<?php echo base_url() ?>webappassets/images/caption.png?v=6-20-13' title='caption'/></a></li><li style='width:33%'><a  class='clone_image' ><img src='<?php echo base_url() ?>webappassets/images/add.png?v=6-20-13' title='add'/></a></li></ul></div><div class='handler_img_width_height'></div>";		//1 image  handler

var handler_image2="<div class='handler_img'><ul><li><a  class='option_image-link'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' title='link'/></a></li><li><a  class='option_image-caption' ><img src='<?php echo base_url() ?>webappassets/images/caption.png?v=6-20-13' title='caption'/></a></li><li><a  style='margin:0px' class='close-clone-link'><img src='<?php echo base_url() ?>webappassets/images/cross-script_large.png?v=6-20-13' title='Delete'/></a></li><li><a  class='clone_image' ><img src='<?php echo base_url() ?>webappassets/images/add.png?v=6-20-13' title='add'/></a></li></ul></div><div class='handler_img_width_height'></div>";		//2 images handler

var handler_image3="<div class='handler_img'><ul><li><a  class='option_image-link'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' title='link'/></a></li><li><a  class='option_image-caption' ><img src='<?php echo base_url() ?>webappassets/images/caption.png?v=6-20-13' title='caption'/></a></li><li><a  style='margin:0px' class='close-clone-link'><img src='<?php echo base_url() ?>webappassets/images/cross-script_large.png?v=6-20-13' title='Delete'/></a></li><li><a  class='clone_image' ><img src='<?php echo base_url() ?>webappassets/images/add.png?v=6-20-13' title='add'/></a></li></ul></div><div class='handler_img_width_height'></div>";		//3 images  handler

var handler_image4="<div class='handler_img'><ul><li style='width:33%'><a  class='option_image-link'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' title='link'/></a></li><li style='width:33%'><a  class='option_image-caption' ><img src='<?php echo base_url() ?>webappassets/images/caption.png?v=6-20-13' title='caption'/></a></li><li style='width:33%'><a  style='margin:0px' class='close-clone-link'><img src='<?php echo base_url() ?>webappassets/images/cross-script_large.png?v=6-20-13' title='Delete'/></a></li></ul></div><div class='handler_img_width_height'></div>";	//4 images  handler

var handler_image_text="<div class='handler_img'><ul><li style='width:33%'><a  class='option_image-link'><img src='<?php echo base_url() ?>webappassets/images/link.png?v=6-20-13' title='link'/></a></li><li style='width:33%'><a  class='option_image-caption' style='float:left;'><img src='<?php echo base_url() ?>webappassets/images/caption.png?v=6-20-13' title='caption'/></a></li><li style='width:33%'><a  class=\"change-pos\" style=\"float:left;\"><img src='<?php echo base_url() ?>webappassets/images/align_right.png?v=6-20-13' title='Right align'/></a></li></ul></div><div class='handler_img_width_height'></div><div class='handler_img_width_height'></div>";	//image with text block  handler

var handler_youtube="<div class='handler_img '><ul><li><a  class=\"edit_youtube-link\" style=\"float:left; display: block; width: 22px; height: 25px;\"><img src=\"<?php echo base_url() ?>webappassets/images/edit_block.png?v=6-20-13\" title=\"edit\"/></a></li></ul></div><div class='handler_img_width_height'></div>";	//Youtube block handler

var handler_header='<div class="menu iconWrap" ><ul><li><a  class="header_link" ><img title="Link"  src="<?php echo base_url();?>webappassets/images/link.png?v=6-20-13"/></a></li><li><a  class="select_theme" ><img title="Change Header"  src="<?php echo base_url();?>webappassets/images/cloud_exchange.png?v=6-20-13"/></a></li><li><a   class="add_logo" ><img title="Add Logo"  src="<?php echo base_url();?>webappassets/images/add_logo.png?v=6-20-13" /></a></li><li><a   class="close-link header_unlink"  ><img title="Delete"  src="<?php echo base_url();?>webappassets/images/cross-script_large.png?v=6-20-13"/></a></li></ul></div>';	// toolbar opions for header

var social_media_header='<li><a title="Edit"  class="edit_social_media-link">edit</a></li>';	//Social media handler

var link_header='<div id="header_link"><img class="header_link_show" name="[header_link_name]"  title="[header_link_title]" src="<?php echo base_url();?>webappassets/images/link.png?v=6-20-13"/></div>';		//header link

var logo_header='<div id="logo" style="left:0px;right:0px"><div class=\'handler_img_width_height\'></div><span class="logo_img"><img id="logo_img_id" src="[logo_src]" title="click to change logo" alt="click to change logo" style="width:100px;"/></span><div  class="logo-resize-div"><div class="handler_logo" ><a class="close-link logo_class" href="javascript:void(0);"><img title="Delete" src="<?php echo base_url();?>webappassets/images/Remove.png?v=6-20-13" border="0"/></a></div><div class="div_border"></div></div></div>';		// Logo html

var handler_footer='<div class="footer_menu iconWrap"><ul><li><a class="edit_footer"  ><img src=\"<?php echo base_url() ?>webappassets/images/edit_block.png?v=6-20-13\" title=\"edit\" border=\"0\"/></a></li></ul></div>';
// Footer handler

var header_border='<div class="resize_header_div" style="position: absolute; top: 0px; left: 0px;display:none;"><div style="border: 1px solid rgb(128, 128, 128);" class="div_border"><span style="display:none;" class="drop_div_border"></span></div></div>';		//Header Image Border

var el_diy_demo_video = '<div style=\"cursor:pointer;\" class=\"diy_demo_video\" onclick=\"javascript:showDIYDemo();\"><img src=\"<?php echo base_url();?>webappassets/images/see-in-action.jpg?v=6-20-13\" align=\"absmiddle\" style=\"-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;\" alt=\"Demo Video\" /></div>';

var handler_offer='<div class=\'handler_img_width_height\'></div>';



/**
	Function drag_drop to drag blocks: Text, Image, Text Image, Divider and  drop  them into email template
**/
function drag_drop(){
	jQuery("#body_main").addClass('drop'); // Add Class drop in body container
	jQuery( ".drop" ).sortable({
		containment: '.main-container',
		revert: true,
		helper:	'clone',
		placeholder:'ui-state-highlight',
		start: function(event, ui) {
			ui.placeholder.height('50px');
			//ui.placeholder.height(ui.item.height());
		},
		over: function(event, ui) {
			$('.diy_demo_video').remove();
			$('.empty_block').removeClass('empty_block');
		},
		out: function(event, ui) {
			//if($('.container-div').length <= 1){
			if($('#body_main').children('.container-div').length < 1){
				$('#body_main').addClass('empty_block');
				$('#body_main').append(el_diy_demo_video);
			}
		},
		update: function(e, ui){
			if (ui.item.hasClass('block-text'))					fnTextBlock(ui.item);
			if (ui.item.hasClass('block-offer'))				fnOfferBlock(ui.item);
			if (ui.item.hasClass('block-image'))				fnImageGroupBlock(ui.item);
			if (ui.item.hasClass('block-image-text'))			fnImageTextBlock(ui.item);
			if (ui.item.hasClass('block-title'))				fnTitleBlock(ui.item);
			if (ui.item.hasClass('block-divider-rule'))			fnDividerRule(ui.item);
			if (ui.item.hasClass('block-social-media'))			fnSocailMedia(ui.item);
			if (ui.item.hasClass('block-youtube'))				fnYoutubeBlock(ui.item);
			jQuery('.save_campaign').removeClass('disable-link');	// enable save link
			pagechange=true;	//Page content change
		}
	});
	jQuery( ".block,.block-image,.block-text,.block-offer,.block-image-text,.block-text, .block-title, .block-image-group,.block-social-media,.block-youtube,.block-divider-rule" ).draggable({ connectToSortable: ".drop",helper: function() {
	var $this= $(this);
	var clone= $this.clone().css("position","relative");
	var cloneWidth= $this.width();
	var helper= $("<div>")
	.append(clone)
	.width(cloneWidth*2)
	.mousemove(function() {
	var lb= 0;
	var q=  cloneWidth;
	var p= 1-Math.max(0,Math.min(1,clone.offset().left / q));
	var lb= 0;
	clone.css("left",(p*50) + "%");
	});
	return helper;
	}, containment: '.main-container', appendTo: ".drop",snap:'true',"distance":0,'revert': false
	});
}

/**
	Function fnTextBlock to drop text block
**/
function fnTextBlock(thisBlock){
	var text_block_id='block_'+new Date().getTime();
	$('.empty_block').removeClass('empty_block');
	var text_content=block_text_content.replace('[text_block_id]',text_block_id);
	thisBlock.after(text_content);
	thisBlock.remove();
	var handler_block=handler.replace('[colspan]','1');
	$('#'+text_block_id).find('.handler_div').prepend(handler_block);
	pagechange=true;	//Page content change
}
/**
	Function fnTitleBlock to drop title block
**/
function fnTitleBlock(thisBlock){
	var title_block_id='block_'+new Date().getTime();
	$('.empty_block').removeClass('empty_block');
	var title_content=block_title_content.replace('[title_block_id]',title_block_id);
	var title_content=title_content.replace('[[colspan]]','1');
	thisBlock.after(title_content);
	thisBlock.remove();
	var handler_block=handler.replace('[colspan]','1');
	$('#'+title_block_id).find('.handler_div').prepend(handler_block);
	pagechange=true;	//Page content change
}
/**
	Function fnImageTextBlock to drop image-text block
**/
function fnImageTextBlock(thisBlock){
	var image_text_block_id='block_'+new Date().getTime();
	var image_block_id=image_text_block_id+"_1";
	$('.empty_block').removeClass('empty_block');
	var image_text_content=block_image_with_text_content.replace('[image_text_block_id]',image_text_block_id);
	image_text_content=image_text_content.replace('[image_block_id]',image_block_id);
	var image_text_content=image_text_content.replace('[colspan]','1');
	thisBlock.after(image_text_content);
	thisBlock.remove();
	var handler_block=handler.replace('[colspan]','1');
	$('#'+image_text_block_id).find('.handler_div').prepend(handler_block);
	$('#'+image_text_block_id).find('.resize_div_text').prepend(handler_image_text);
	imageResize(image_text_block_id);
	loadImageSize(image_text_block_id);
	pagechange=true;	//Page content change
	dragDropImageBank();
}
/**
	Function fnImageGroupBlock to drop image group block
**/
function fnImageGroupBlock(thisBlock){
	var img_id='block_'+new Date().getTime();
	thisBlock.attr('id',img_id);
	$.fancybox($("#image_group_dialog").html(),{'autoDimensions':false,'height':'290','width':'450','centerOnScroll':true,'onClosed':function() {CloseImageGroupBlock();}});	//open poppup
	$("#current_container_id").val(img_id);		// set block id
	pagechange=true;	//Page content change
}
/**
	Function fnDividerRule to drop divider  block
**/
function fnDividerRule(thisBlock){
	var divider_block_id='block_'+new Date().getTime();
	$('.empty_block').removeClass('empty_block');
	var divider_content=block_divider_content.replace('[divider_block_id]',divider_block_id);
	thisBlock.after(divider_content);
	thisBlock.remove();
	var handler_block=handler.replace('[colspan]','1');
	$('#'+divider_block_id).find('.handler_div').prepend(handler_block);
	pagechange=true;	//Page content change
}
/**
	Function fnOfferBlock to drop offer  block
**/
function fnOfferBlock(thisBlock){
	var offer_block_id='block_'+new Date().getTime();
	$('.empty_block').removeClass('empty_block');
	var offer_content=block_offer_content.replace('[offer_block_id]',offer_block_id);
	thisBlock.after(offer_content);
	thisBlock.remove();
	var handler_block=handler.replace('[colspan]','1');
	$('#'+offer_block_id).find('.handler_div').prepend(handler_block);
	$('#'+offer_block_id).find('.edit_offer_div').prepend(handler_offer);
	pagechange=true;	//Page content change
	loadOfferEffects(offer_block_id);
}
/**
	Function fnSocailMedia to drop social media block
**/
function fnSocailMedia(thisBlock){
	var img_id='block_'+new Date().getTime();
	thisBlock.attr('id',img_id);
	$.fancybox($("#social_media_dialog").html(),{'autoDimensions':false,'height':'485','width':'840','centerOnScroll':true,'onClosed':function() {closeSocialMedia();}});	//open poppup
	$("#current_container_id").val(img_id);		// set block id
	pagechange=true;	//Page content change
}
/**
	Function fnYoutubeBlock to drop youtube block or vimeo video
**/
function fnYoutubeBlock(thisBlock){
	var img_id= new Date().getTime();
	thisBlock.attr('id',img_id);
	$.fancybox($("#youtube_edit_dialog").html(),{'autoDimensions':false,'height':'244','width':'475','centerOnScroll':true,'onClosed':function() {close_youtube();}});	//open poppup
	$("#current_container_id").val(img_id);		// set block id
	pagechange=true;	//Page content change
}
/**
	function saveImageGroupOption to insert  images in image group block
**/
function saveImageGroupOption(img_count){
	var img_id='block_'+new Date().getTime();
	var img_html='';
	var top_div;
	if($("#current_container_id").val()!=""){
		top_div=$("#current_container_id").val();
	}
	if(img_count==1){
		var image_block_id='block_'+new Date().getTime();
		var image1_block_id=image_block_id+"_1";
		$('.empty_block').removeClass('empty_block');
		var image_content=block_image_content.replace('[image_block_id]',image_block_id);
		image_content=image_content.replace('[image1_block_id]',image1_block_id);
		jQuery("#"+top_div).after(image_content);
		jQuery("#"+top_div).remove();
		var handler_block=handler.replace('[colspan]','1');
		$('#'+image_block_id).find('.handler_div').prepend(handler_block);
		$('#'+image_block_id).find('.resize_div').prepend(handler_image1);
		loadImageSize(image_block_id);
		imageResize(image_block_id);
		pagechange=true;	//Page content change
	}else if(img_count==2){
		var image_block_id='block_'+new Date().getTime();
		var image1_block_id='block_'+new Date().getTime()+"_1";
		var image2_block_id='block_'+new Date().getTime()+"_2";
		var image_content="";
		$('.empty_block').removeClass('empty_block');
		image_content=block_image2_content.replace('[image_block_id]',image_block_id);
		image_content=image_content.replace('[image1_block_id]',image1_block_id);
		image_content=image_content.replace('[image2_block_id]',image2_block_id);
		jQuery("#"+top_div).after(image_content);
		jQuery("#"+top_div).remove();
		var handler_block=handler.replace('[colspan]','2');
		$('#'+image_block_id).find('.handler_div').prepend(handler_block);
		$('#'+image1_block_id).find('.resize_div').prepend(handler_image4);
		$('#'+image2_block_id).find('.resize_div').prepend(handler_image2);
		loadImageSize(image_block_id);
		imageResize(image_block_id);
		pagechange=true;	//Page content change
	}else if(img_count==3){
		var image_block_id='block_'+new Date().getTime();
		var image1_block_id='block_'+new Date().getTime()+"_1";
		var image2_block_id='block_'+new Date().getTime()+"_2";
		var image3_block_id='block_'+new Date().getTime()+"_3";
		var image_content="";
		$('.empty_block').removeClass('empty_block');
		image_content=block_image3_content.replace('[image_block_id]',image_block_id);
		image_content=image_content.replace('[image1_block_id]',image1_block_id);
		image_content=image_content.replace('[image2_block_id]',image2_block_id);
		image_content=image_content.replace('[image3_block_id]',image3_block_id);
		jQuery("#"+top_div).after(image_content);
		jQuery("#"+top_div).remove();
		var handler_block=handler.replace('[colspan]','3');
		$('#'+image_block_id).find('.handler_div').prepend(handler_block);
		$('#'+image1_block_id).find('.resize_div').prepend(handler_image4);
		$('#'+image2_block_id).find('.resize_div').prepend(handler_image4);
		$('#'+image3_block_id).find('.resize_div').prepend(handler_image2);
		loadImageSize(image_block_id);
		imageResize(image_block_id);
		pagechange=true;	//Page content change
	}else if(img_count==4){
		var image_block_id='block_'+new Date().getTime();
		var image1_block_id='block_'+new Date().getTime()+"_1";
		var image2_block_id='block_'+new Date().getTime()+"_2";
		var image3_block_id='block_'+new Date().getTime()+"_3";
		var image4_block_id='block_'+new Date().getTime()+"_4";
		var image_content="";
		$('.empty_block').removeClass('empty_block');
		image_content=block_image4_content.replace('[image_block_id]',image_block_id);
		image_content=image_content.replace('[image1_block_id]',image1_block_id);
		image_content=image_content.replace('[image2_block_id]',image2_block_id);
		image_content=image_content.replace('[image3_block_id]',image3_block_id);
		image_content=image_content.replace('[image4_block_id]',image4_block_id);
		jQuery("#"+top_div).after(image_content);
		jQuery("#"+top_div).remove();
		var handler_block=handler.replace('[colspan]','4');
		$('#'+image_block_id).find('.handler_div').prepend(handler_block);
		$('#'+image1_block_id).find('.resize_div').prepend(handler_image4);
		$('#'+image2_block_id).find('.resize_div').prepend(handler_image4);
		$('#'+image3_block_id).find('.resize_div').prepend(handler_image4);
		$('#'+image4_block_id).find('.resize_div').prepend(handler_image4);
		loadImageSize(image_block_id);
		imageResize(image_block_id);
		pagechange=true;	//Page content change
	}
	$('.empty_block').removeClass('empty_block');
	$.fancybox.close();
	setTimeout('dragDropImageBank()', 1000);
}
/**
	Function CloseImageGroupBlock to close image group popup
**/
function CloseImageGroupBlock(){
	$('.diy_demo_video').remove();
	if($("#current_container_id").val()!=""){
		var top_div=$("#current_container_id").val();
		jQuery("#"+top_div).remove();
	}
}
/**
	Function socialMediaUrlSubmit to save social media url
**/
function socialMediaUrlSubmit(){
	var social_media_link="";
	var top_div="";
	var colspan=0;
	if($("#current_container_id").val()!=""){
		top_div=$("#current_container_id").val();
		$("#current_container_id").val("");
	}
	if(($('#fancybox-wrap').find('#facebook_link:checked').val())&&($('#fancybox-wrap').find("#facebox_url").val())){
		if( -1 == $('#fancybox-wrap').find("#facebox_url").val().indexOf('http'))
		{
			var facebook_link ='http://'+$('#fancybox-wrap').find("#facebox_url").val();
		}else{
			var facebook_link =$('#fancybox-wrap').find("#facebox_url").val();
		}
		colspan++;
		var facebook_cnt=block_social_facebook_content.replace('[facebook_link]',facebook_link);
		facebook_cnt=facebook_cnt.replace('[facebook_link]',facebook_link);
		facebook_cnt=facebook_cnt.replace('[facebook_link]',facebook_link);
		social_media_link+=facebook_cnt;
	}
	if(($('#fancybox-wrap').find('#twitter_link:checked').val())&&($('#fancybox-wrap').find("#twitter_url").val())){
		if( -1 == $('#fancybox-wrap').find("#twitter_url").val().indexOf('http') )
		{
			var twitter_link ='http://'+$('#fancybox-wrap').find("#twitter_url").val();
		}else{
			var twitter_link =$('#fancybox-wrap').find("#twitter_url").val();
		}
		colspan++;
		var twitter_cnt=block_social_twitter_content.replace('[twitter_link]',twitter_link);
		twitter_cnt=twitter_cnt.replace('[twitter_link]',twitter_link);
		twitter_cnt=twitter_cnt.replace('[twitter_link]',twitter_link);
		social_media_link+=twitter_cnt;
	}
	if(($('#fancybox-wrap').find('#linkedin_link:checked').val())&&($('#fancybox-wrap').find("#linkedin_url").val())){
		if( -1 == $('#fancybox-wrap').find("#linkedin_url").val().indexOf('http') )
		{
			var linkedin_link ='http://'+$('#fancybox-wrap').find("#linkedin_url").val();
		}else{
			var linkedin_link =$('#fancybox-wrap').find("#linkedin_url").val();
		}
		colspan++;
		var linkedin_cnt=block_social_linkedin_content.replace('[linkedin_link]',linkedin_link);
		linkedin_cnt=linkedin_cnt.replace('[linkedin_link]',linkedin_link);
		linkedin_cnt=linkedin_cnt.replace('[linkedin_link]',linkedin_link);
		social_media_link+=linkedin_cnt;
	}
	if(($('#fancybox-wrap').find('#rss_link:checked').val())&&($('#fancybox-wrap').find("#rss_url").val())){
		if( -1 == $('#fancybox-wrap').find("#rss_url").val().indexOf('http') )
		{
			var rss_link ='http://'+$('#fancybox-wrap').find("#rss_url").val();
		}else{
			var rss_link=$('#fancybox-wrap').find("#rss_url").val();
		}
		colspan++;
		var rss_cnt=block_social_rss_content.replace('[rss_link]',rss_link);
		rss_cnt=rss_cnt.replace('[rss_link]',rss_link);
		rss_cnt=rss_cnt.replace('[rss_link]',rss_link);
		social_media_link+=rss_cnt;
	}
	if(($('#fancybox-wrap').find('#youtube_link:checked').val())&&($('#fancybox-wrap').find("#youtube_url").val())){
		if( -1 == $('#fancybox-wrap').find("#youtube_url").val().indexOf('http') )
		{
			var youtube_link ='http://'+$('#fancybox-wrap').find("#youtube_url").val();
		}else{
			var youtube_link =$('#fancybox-wrap').find("#youtube_url").val();
		}
		colspan++;
		var youtube_cnt=block_social_youtube_content.replace('[youtube_link]',youtube_link);
		youtube_cnt=youtube_cnt.replace('[youtube_link]',youtube_link);
		youtube_cnt=youtube_cnt.replace('[youtube_link]',youtube_link);
		social_media_link+=youtube_cnt;
	}
	if(($('#fancybox-wrap').find('#google_plus_link:checked').val())&&($('#fancybox-wrap').find("#google_plus_url").val())){
		if( -1 == $('#fancybox-wrap').find("#google_plus_url").val().indexOf('http') )
		{
			var google_plus_link ='http://'+$('#fancybox-wrap').find("#google_plus_url").val();
		}else{
			var google_plus_link =$('#fancybox-wrap').find("#google_plus_url").val();
		}
		colspan++;
		var google_plus_cnt=block_social_google_plus_content.replace('[google_plus_link]',google_plus_link);
		google_plus_cnt=google_plus_cnt.replace('[google_plus_link]',google_plus_link);
		google_plus_cnt=google_plus_cnt.replace('[google_plus_link]',google_plus_link);
		social_media_link+=google_plus_cnt;
	}
	if(($('#fancybox-wrap').find('#tumblr_link:checked').val())&&($('#fancybox-wrap').find("#tumblr_url").val())){
		if( -1 == $('#fancybox-wrap').find("#tumblr_url").val().indexOf('http') )
		{
			var tumblr_link ='http://'+$('#fancybox-wrap').find("#tumblr_url").val();
		}else{
			var tumblr_link =$('#fancybox-wrap').find("#tumblr_url").val();
		}
		colspan++;
		var tumblr_cnt=block_social_tumblr_content.replace('[tumblr_link]',tumblr_link);
		tumblr_cnt=tumblr_cnt.replace('[tumblr_link]',tumblr_link);
		tumblr_cnt=tumblr_cnt.replace('[tumblr_link]',tumblr_link);
		social_media_link+=tumblr_cnt;
	}
	if(($('#fancybox-wrap').find('#flickr_link:checked').val())&&($('#fancybox-wrap').find("#flickr_url").val())){
		if( -1 == $('#fancybox-wrap').find("#flickr_url").val().indexOf('http') )
		{
			var flickr_link ='http://'+$('#fancybox-wrap').find("#flickr_url").val();
		}else{
			var flickr_link =$('#fancybox-wrap').find("#flickr_url").val();
		}
		colspan++;
		var flickr_cnt=block_social_flickr_content.replace('[flickr_link]',flickr_link);
		flickr_cnt=flickr_cnt.replace('[flickr_link]',flickr_link);
		flickr_cnt=flickr_cnt.replace('[flickr_link]',flickr_link);
		social_media_link+=flickr_cnt;
	}
	if(($('#fancybox-wrap').find('#skype_link:checked').val())&&($('#fancybox-wrap').find("#skype_url").val())){
		 
		var skype_link ='skype:'+ ($('#fancybox-wrap').find("#skype_url").val()).split("skype:").join("");
		 
		colspan++;
		var skype_cnt=block_social_skype_content.replace('[skype_link]',skype_link);
		skype_cnt=skype_cnt.replace('[skype_link]',skype_link);
		skype_cnt=skype_cnt.replace('[skype_link]',skype_link);
		social_media_link+=skype_cnt;
	}
	if(($('#fancybox-wrap').find('#pinterest_link:checked').val())&&($('#fancybox-wrap').find("#pinterest_url").val())){
		if( -1 == $('#fancybox-wrap').find("#pinterest_url").val().indexOf('http') )
		{
			var pinterest_link ='http://'+$('#fancybox-wrap').find("#pinterest_url").val();
		}else{
			var pinterest_link =$('#fancybox-wrap').find("#pinterest_url").val();
		}
		colspan++;
		var pinterest_cnt=block_social_pinterest_content.replace('[pinterest_link]',pinterest_link);
		pinterest_cnt=pinterest_cnt.replace('[pinterest_link]',pinterest_link);
		pinterest_cnt=pinterest_cnt.replace('[pinterest_link]',pinterest_link);
		social_media_link+=pinterest_cnt;
	}
	if(($('#fancybox-wrap').find('#instagram_link:checked').val())&&($('#fancybox-wrap').find("#instagram_url").val())){
		if( -1 == $('#fancybox-wrap').find("#instagram_url").val().indexOf('http') )
		{
			var instagram_link ='http://'+$('#fancybox-wrap').find("#instagram_url").val();
		}else{
			var instagram_link =$('#fancybox-wrap').find("#instagram_url").val();
		}
		colspan++;
		var instagram_cnt=block_social_instagram_content.replace('[instagram_link]',instagram_link);
		instagram_cnt=instagram_cnt.replace('[instagram_link]',instagram_link);
		instagram_cnt=instagram_cnt.replace('[instagram_link]',instagram_link);
		social_media_link+=instagram_cnt;
	}
	if(($('#fancybox-wrap').find('#mailto_link:checked').val())&&($('#fancybox-wrap').find("#mailto_url").val())){
		if( -1 == $('#fancybox-wrap').find("#mailto_url").val().indexOf('http') )
		{
			var mailto_link ='mailto:'+$('#fancybox-wrap').find("#mailto_url").val().split("mailto:").join("");
		}else{
			var mailto_link =$('#fancybox-wrap').find("#mailto_url").val();
		}
		colspan++;
		var mailto_cnt=block_mailto_content.replace('[mailto_link]',mailto_link);
		mailto_cnt=mailto_cnt.replace('[mailto_link]',mailto_link);
		mailto_cnt=mailto_cnt.replace('[mailto_link]',mailto_link);
		social_media_link+=mailto_cnt;
	}
	if(($('#fancybox-wrap').find('#website_link:checked').val())&&($('#fancybox-wrap').find("#website_url").val())){
		if( -1 == $('#fancybox-wrap').find("#website_url").val().indexOf('http') )
		{
			var website_link ='http://'+$('#fancybox-wrap').find("#website_url").val();
		}else{
			var website_link =$('#fancybox-wrap').find("#website_url").val();
		}
		colspan++;
		var website_cnt=block_website_content.replace('[website_link]',website_link);
		website_cnt=website_cnt.replace('[website_link]',website_link);
		website_cnt=website_cnt.replace('[website_link]',website_link);
		social_media_link+=website_cnt;
	}
	if(social_media_link!=""){
		if(top_div!=""){
			var social_block_id='block_'+new Date().getTime();
			var social_block_tag=social_media_link_open_tag.replace('[social_block_id]',social_block_id);
			$('#'+top_div).after(social_block_tag+social_media_link+social_media_link_close_tag);
			$('#'+top_div).remove();
			var handler_block=handler.replace('[colspan]','1');
			$('#'+social_block_id).find('.handler_div').prepend(handler_block);
			$('#'+social_block_id).find('.handler').addClass('handler_social_media');
			$('#'+social_block_id).find('.handler').find('.drag-center').addClass('drag_social_li');
			$('#'+social_block_id).find('.handler').find('.close-link').parent().after(social_media_header);
		}
		$('.empty_block').removeClass('empty_block');		//remove empty block from conatiner
	}
	$.fancybox.close();		// close popup
	pagechange=true;	//Page content change
}
/**
	function closeSocialMedia
**/
function closeSocialMedia(){
	$('.diy_demo_video').remove();
	var top_div=$('#body_main').find('.block-social-media').attr('id');
	if($('#'+top_div).find('.social_media_link').length<=0){
		$("#"+top_div).remove();
	}
}
/**
	Function checkYoutubevideoOrVimeovideo to check enter url is for youtube or for vimeo video
**/
function checkYoutubevideoOrVimeovideo(){

	var url="";
	url=$('#fancybox-wrap').find("#youtube_url").val();
	var matches = url.match(/^(https?:\/\/)?([^\/]*\.)?youtube\.com\/watch\?([^]*&)?v=\-\w+(&[^]*)?/i);
	if(matches){
		youtubeUrlUpdate()
	}else{
		vimeoVideoUrlUpdate();
	}
//$.unblockUI();
}
/**
	function youtubeUrlUupdate to save youtube url
**/
function youtubeUrlUpdate(){
$('#fancybox-wrap').find('.youtube_msg').show();
$('#fancybox-wrap').find('.youtube_msg').html("Please wait...");
	var url="";
	url=$('#fancybox-wrap').find("#youtube_url").val();
	var matches = url.match(/^(https?:\/\/)?([^\/]*\.)?youtube\.com\/watch\?([^]*&)?v=\-\w+(&[^]*)?/i);
	if(matches){
		var top_div_arr=$("#current_container_id").val().split('_');
		var top_div=top_div_arr[0];
		if(top_div_arr[1]=="edit"){
			$('#'+top_div).html('');
		}
		if(url!=""){
			var img_id='block_'+new Date().getTime();
			$("#"+top_div).attr('name',url);
			var video_id =url.split('v=')[1];
			var ampersandPosition = video_id.indexOf('&');
			if(ampersandPosition != -1) {
				video_id = video_id.substring(0, ampersandPosition);
			}
			// http://salman-w.blogspot.in/2010/01/retrieve-youtube-video-title.html
			var ytApiKey = "AIzaSyAlDZx2H4e1r035CjWftUHNJQr_iu8P0BM";			 
			$.getJSON("https://www.googleapis.com/youtube/v3/videos", {
					key: ytApiKey,
					part: "snippet,statistics",
					id: video_id
				}, function(data) {
					if (data.items.length === 0) {
					}else{
						youtubeFeedCallback(data);
					}
				});	
		}else if($('#'+top_div).find('.youtube_link').length<=0){
			$("#"+top_div).remove();
			$.fancybox.close();
		}
	}else{
		$('#fancybox-wrap').find('.youtube_msg').show();
		$('#fancybox-wrap').find('.youtube_msg').html("Please Enter Valid url");
		setTimeout( function(){$('#fancybox-wrap').find('.youtube_msg').fadeOut();} , 4000);
	}
	pagechange=true;	//Page content change
}
/**
	Function to display youtube image and title
**/
function youtubeFeedCallback(json){
	if(typeof json["error"] !== 'undefined'){
		close_youtube();
		//$("#"+top_div).remove();
		data='<div style="display:block;margin:20px;">Right now, we are unable to get video details for this URL.</div>';
		$.fancybox(data,{'autoDimensions':false,'height':'auto','width':'480','centerOnScroll':true});
		//$.fancybox.close();
	}else{
	$('.diy_demo_video').remove();
	$('#body_main').removeClass('empty_block');
	var top_div_arr=$("#current_container_id").val().split('_');
	var top_div=top_div_arr[0];
	var youtube_block_id=new Date().getTime();
	var youtube1_block_id=youtube_block_id+"_1";
	var blok_youtube=blok_youtube_content.replace('[image_block_id]',youtube_block_id);
	blok_youtube=blok_youtube.replace('[image1_block_id]',youtube1_block_id);
	//var img_src=json["data"]["thumbnail"]["hqDefault"];
	var img_src=json.items[0].snippet.thumbnails.medium.url; 
	img_src = img_src.replace('http://','https://');
	blok_youtube=blok_youtube.replace('[youtube_img]',img_src);
	var youtube_link=$("#"+top_div).attr('name');
	blok_youtube=blok_youtube.replace('[youtube_link]',youtube_link);
	$("#"+top_div).after(blok_youtube);
	var handler_block=handler.replace('[colspan]','1');
	$('#'+youtube_block_id).find('.handler_div').prepend(handler_block);
	$('#'+youtube_block_id).find('.resize_div').prepend(handler_youtube);
	//$("#"+youtube_block_id).find('.youtube_image_caption').html(json["data"]["title"]);
	$("#"+youtube_block_id).find('.youtube_image_caption').html(json.items[0].snippet.title);
	$("#"+youtube_block_id).find('.image_link').attr('title',youtube_link);
	var delay2 = function() { youtubeImageAspectRatio(youtube1_block_id); };
	setTimeout(delay2, 1000);
	$.fancybox.close();
	$('#'+top_div).remove();
	pagechange=true;	//Page content change
	}
}
/**
	function youtubeImageAspectRatio to calculate youtube image aspect ratio
**/
function youtubeImageAspectRatio(youtube_img_id){
	var width=$('#'+youtube_img_id).find('.image-container').width();
	var height=$('#'+youtube_img_id).find('.image-container').height();

	if((width>0)&&(height>0)){
		var aspect_ratio=width/height;
		$('#'+youtube_img_id).find('.image-container').attr('name',aspect_ratio);
		var delay1 = function() { loadImageSize(youtube_img_id); };
		setTimeout(delay1, 2000);
		var delay = function() { imageResize(youtube_img_id); };
		setTimeout(delay, 2000);
		$('#'+youtube_img_id).find('.video_play').css({'top':(height-41)/2+'px','left':(width-58)/2+'px'});
	}else{
		youtubeImageAspectRatio(youtube_img_id);
	}
}
/**
	Function close_youtube to close youtube dialog box
**/
function close_youtube(){
	var top_div=$("#current_container_id").val();
	if($('#'+top_div).find('.youtube_link').length<=0){
		$("#"+top_div).remove();
	}
}
/**
	Function vimeoVideoUrlUpdate to add vimeo video on campaign
**/
function vimeoVideoUrlUpdate(){
	var url="";
	url=$('#fancybox-wrap').find("#youtube_url").val();
	var vimeo_url="http://vimeo.com/";
	var video_id=url.substring(vimeo_url.length);
	jQuery.ajax({
		url: "<?php echo base_url() ?>ajax/get_vimeo_video_image/"+video_id,
		type:"POST",
		success: function(data) {
			var data_obj=jQuery.parseJSON(data);
			if(data_obj.error){
				$('#fancybox-wrap').find('.youtube_msg').show();
				$('#fancybox-wrap').find('.youtube_msg').html(data_obj.error);
				setTimeout( function(){$('#fancybox-wrap').find('.youtube_msg').fadeOut();} , 4000);
			}else{
				var vimeo_video_url=data_obj.image_path;
				$('#body_main').removeClass('empty_block');
				var top_div_arr=$("#current_container_id").val().split('_');
				var top_div=top_div_arr[0];
				var vimeo_video_block_id=new Date().getTime();
				var vimeo_video1_block_id=vimeo_video_block_id+"_1";
				var blok_vimeo_video=blok_vimeo_video_content.replace('[image_block_id]',vimeo_video_block_id);
				blok_vimeo_video=blok_vimeo_video.replace('[image1_block_id]',vimeo_video1_block_id);
				blok_vimeo_video=blok_vimeo_video.replace('[vimeo_video_img]',vimeo_video_url);
				blok_vimeo_video=blok_vimeo_video.replace('[vimeo_video_link]',url);
				$("#"+top_div).after(blok_vimeo_video);
				var handler_block=handler.replace('[colspan]','1');
				$('#'+vimeo_video_block_id).find('.handler_div').prepend(handler_block);
				$('#'+vimeo_video_block_id).find('.resize_div').prepend(handler_youtube);
				$("#"+vimeo_video_block_id).find('.youtube_image_caption').html(data_obj.title);
				$("#"+vimeo_video_block_id).find('.image_link').attr('title',url);
				var delay2 = function() { youtubeImageAspectRatio(vimeo_video1_block_id); };
				setTimeout(delay2, 1000);
				$.fancybox.close();
				$('.diy_demo_video').remove();
				$('#'+top_div).remove();
				pagechange=true; 	//Page content change
			}
		}
	});
}
/**
	Function saveImageCaption to add caption on image
**/
function saveImageCaption(){
	var image_caption=$('#fancybox-wrap').find("#image_link_caption").val();
	var img_conatiner_id=$('#active_block').parents('.img_content').attr('id');
	$('#'+img_conatiner_id).find('.image_caption').html(image_caption);
	$('#active_block').attr("id","");
	image_block_id=$('#'+img_conatiner_id).parents('.container-div').attr('id');
	// ----------------------
	var container_min_height =$('#'+image_block_id).innerHeight();
	$('#'+image_block_id).find('.text-paragraph-container').css('min-height',container_min_height+'px');
	// ----------------------

	$.fancybox.close();
	pagechange=true;	//Page content change
}
/**
	Function to add link on image
**/
function saveImageLink(){
	var image_link="";
	if( -1 == $('#fancybox-wrap').find("#image_link").val().indexOf('http') ){
		image_link ='http://'+$('#fancybox-wrap').find("#image_link").val();
	}else{
		image_link =$('#fancybox-wrap').find("#image_link").val();
	}
	if($('#fancybox-wrap').find("#image_link").val()){
		var img_conatiner_id=$('#active_block').parents('.img_content').attr('id');
		$('#'+img_conatiner_id).find('.image_link').attr("title",image_link);
		$('#'+img_conatiner_id).find('.image_link').attr("name",image_link);
		$('#'+img_conatiner_id).find('.image_link').show();
	}else{
		var img_conatiner_id=$('#active_block').parents('.img_content').attr('id');
		$('#'+img_conatiner_id).find('.image_link').hide();
		$('#'+img_conatiner_id).find('.image_link').attr("title","");
		$('#'+img_conatiner_id).find('.image_link').attr("name","");
	}
	$('#active_block').attr("id","");
	$.fancybox.close();
	pagechange=true;	//Page content change
}

/**
	Function loadHeaderEffect to load header effects
**/
function loadHeaderEffect(){
	$('.header_div').find('.menu').remove();
	$('.header_div').find('.resize_header_div').remove();
	$('.header_div').find('.resize_div').remove();
	$('.header_div').prepend(handler_header);
	$('#header').find('.header_div').append(header_border);
	// If header is empty then load drop-header image
	if($('.empty_header').length>0){
		if(jQuery("#current_template_id").val()!=-1){
			var header_img_path='<img src="<?php echo $this->config->item('webappassets');?>header-images/header-'+jQuery("#current_template_id").val()+'.jpg" border="0" class="header_img"  />';
			jQuery('#header').append(header_img_path);
			jQuery('.empty_header').remove();
		}else{
			$('.empty_header').html('<img src="<?php echo $this->config->item('webappassets');?>images/drop-header.png?v=6-20-13" />');
			$('.header_link').css('display','none');
			$('.add_logo').css('display','none');
			$('#logo').remove();
			$('#header_link').remove();
			$('.header_link').parent().remove();
			$('.add_logo').parent().remove();
		}
	}
	var delay = function() { setHeaderHeight(); };
	setTimeout(delay, 1000);
}
/**
	Function setHeaderHeight to set header height
**/
function setHeaderHeight(){
	var img_height=0;
	if($('.empty_header').length>0){
		img_height=$('.empty_header').find('img').height();
	}else{
		img_height=$('.header_img').height();
	}
	$('#header').find('.resize_header_div').width('595');
	$('#header').find('.resize_header_div').height(img_height);
	$('.header_img').attr('height',img_height);
	$('.header_img').attr('width','595');
}
/**
	Function dragDropImageBank to drag image from image bank and drop them to Image block or to Header of email conatainer
**/
function dragDropImageBank(){
	jQuery( ".draggable1").draggable({
		helper:'clone',
		containment: '#main-table',
		appendTo: "#body_main",
		snap:true,
		zIndex:15,
		revert: false,
		cursor:'move',
		drag: function(event, ui) {
			jQuery('.handler_img').hide();
			jQuery('.handler').hide();
			jQuery('.container-div').removeClass('highlighted');
		}
	});
	jQuery( ".position_div,#header" ).droppable({
		'tolerance':'touch',
		accept:".draggable1",
		over: function(event, ui) {
			jQuery(this).find('.resize_div').find('.highlight_on_image_hover').css("border","5px solid #808080");
			jQuery(this).find('.resize_div').show();
			jQuery(this).find('.resize_header_div').find('.div_border').css("border","5px solid #808080");
			jQuery(this).find('.resize_header_div').show();
			jQuery(this).find('.resize_div_text').find('.highlight_on_image_hover').css("border","5px solid #808080");
			jQuery(this).find('.resize_div_text').show();
		},
		out: function(event, ui) {
			jQuery(this).find('.resize_div').find('.highlight_on_image_hover').css("border","none");
			jQuery(this).find('.resize_div').hide();
			jQuery(this).find('.resize_div_text').find('.highlight_on_image_hover').css("border","none");
			jQuery(this).find('.resize_header_div').find('.div_border').css("border","none");
			jQuery(this).find('.resize_header_div').hide();
			jQuery(this).find('.resize_div_text').hide();
			jQuery('#header').css("border","none");
		},
		drop: function( event, ui ) {
			jQuery(this).find('.resize_div').find('.div_border').css("border","1px solid #808080");
			jQuery(this).find('.resize_div_text').find('.div_border').css("border","1px solid #808080");
			jQuery(this).find('.resize_header_div').find('.div_border').css("border","1px solid #808080");
			jQuery(this).find('.resize_div').find('.highlight_on_image_hover').css("border","none");
			jQuery(this).find('.resize_div_text').find('.highlight_on_image_hover').css("border","none");
			jQuery('#header').css("border","none");
			jQuery(this).find('.resize_div').hide();
			jQuery(this).find('.resize_div_text').hide();
			jQuery(this).find('.resize_header_div').hide();
			var image_block=ui.draggable.clone();
			var img_src=jQuery(image_block).attr('src');
			if(jQuery(this).find('.header_div').length>0){
				var header_img='<img border="0" class="header_img" src="'+img_src+'" width="595" height="auto" />';
				jQuery(this).find('.header_img').remove();
				jQuery('#header').append(header_img);
				jQuery('.empty_header').remove();
				$('.header_div').find('.menu').remove();
				$('.header_div').prepend(handler_header);
				var delay = function() { setHeaderHeight(); };
				setTimeout(delay, 1000);
			}else{
				jQuery(this).find('.image-container').attr('src',img_src);
				var img_info=jQuery(image_block).attr('name');
				var img_array=img_info.split(",",3);
				var aspect_ratio=parseFloat(img_array[1]/img_array[2]);
				jQuery(this).find('.image-container').attr('name',aspect_ratio);
				var img_filename = img_src.substring(img_src.lastIndexOf("-")+1,img_src.lastIndexOf("."));
				jQuery(this).find('.image-container').attr('alt',img_filename);
				var element_id=jQuery(this).parents('.container-div').attr('id');
				loadImageSize(element_id);
			}
			pagechange=true;	//Page content change
		}
	});
}
function organizeImageBank() {
	$('.img-bank').masonry({
    itemSelector : '.li_draggable',
    columnWidth : 120
  });
	$('.img-bank').find("img").each(function() {
		$(this).load(function() {
  		$('.img-bank').masonry('reload');
		});
	});
}
/**
	Function loadImageEffect to calculate width and height of drop image according to aspect ratio
**/
function loadImageEffect(img_obj){
	var width=img_obj.width();
	var height=img_obj.height();
	var aspect_ratio=img_obj.attr('name');
	var new_height=width/aspect_ratio;
	img_obj.height(new_height);
}
/**
	Function resizeCloneImages to calculate width and height of clone images according to aspect ratio
**/
function resizeCloneImages(div_id,clone_length){
	var element_obj=$('#'+div_id).find('.img_content');
	if(clone_length==1){//image-container drop-image
		element_obj.each(function() {
			var width=275;
			var aspect_ratio=$(this).find('.image-container').attr('name');
			var new_height=width/aspect_ratio;
			$(this).find('.image-container').height(new_height);
			$(this).find('.image-container').width(width);
		});
	}else if(clone_length==2){
		element_obj.each(function() {
			var width=265;
			var aspect_ratio=$(this).find('.image-container').attr('name');
			var new_height=width/aspect_ratio;
			$(this).find('.image-container').height(new_height);
			$(this).find('.image-container').width(width);
		});
	}else if(clone_length==3){
		element_obj.each(function() {
			var width=168;
			var aspect_ratio=$(this).find('.image-container').attr('name');
			var new_height=width/aspect_ratio;
			$(this).find('.image-container').height(new_height);
			$(this).find('.image-container').width(width);
		});
	}else if(clone_length==4){
		element_obj.each(function() {
			var width=120;
			var aspect_ratio=$(this).find('.image-container').attr('name');
			var new_height=width/aspect_ratio;
			$(this).find('.image-container').height(new_height);
			$(this).find('.image-container').width(width);
		});
	}
}

/**
   Function to change the theme on select of category from drop down box
**/
function changeTheme(){
	$.ajax({
        type        : "POST",
        cache       : false,
        url         : base_url+'newsletter/campaign/get_theme_data_for_ajax/'+$('#fancybox-wrap').find("#category_list").val()+'',
        data        : $(this).serializeArray(),
        success: function(data) {
            $.fancybox(data,{'autoDimensions':false,'height':'auto','width':'680','centerOnScroll':true});
        }
    });
}

/**
	Function saveHeader to change Header Image
**/
saveHeader=function(template_id){
	jQuery('#header').find('.header_img').remove();
	var header_img_path='<img src="<?php echo $this->config->item('webappassets');?>header-images/header-'+template_id+'.jpg" border="0" class="header_img"  width="595" height="auto" />';
	jQuery('#header').append(header_img_path);
	jQuery('.empty_header').remove();
	$('.header_div').find('.menu').remove();
	$('.header_div').prepend(handler_header);
	$.fancybox.close();
	pagechange=true;	//Page content change
	var delay = function() { setHeaderHeight(); };
	setTimeout(delay, 1000);
}

/**
	Function saveHeaderLink to add link on header
**/
function saveHeaderLink(){

	if($('#fancybox-wrap').find("#header_link_text").val()){
		setTimeout($('.header_div').find('#header_link').empty().remove(),1000);

		if( -1 == $('#fancybox-wrap').find("#header_link_text").val().indexOf('http') )
		{
			var header_link_url ='http://'+$('#fancybox-wrap').find("#header_link_text").val();
		}else{
			var header_link_url =$('#fancybox-wrap').find("#header_link_text").val();
		}

		//var link_text = $(link_header).find('.header_link_show');
		// link_text.attr('name',header_link_url);
		// link_text.attr('title',header_link_url);


		var link_text=link_header.replace('[header_link_name]',header_link_url);
		link_text=link_text.replace('[header_link_title]',header_link_url);


		$('.header_div').prepend(link_text);
	}else{
		$('#header_link').remove();
	}
	$.fancybox.close();
	pagechange=true;	//Page content change
}

/**
*	Function unlinkImageBank to remove image from image bank
*	param (int) (img_id)  image id
*/
function unlinkImageBank(img_id){
	jQuery.ajax({
		url: "<?php echo base_url() ?>ajax/unlink_image_bank/"+img_id,
		type:"POST",
		success: function(data) {
			jQuery("#"+img_id).parents('.li_draggable').remove();
			if($('.image_bank_div').length<=0){
				var no_img='<li class="load_images"><b>Click "Upload Images" button to upload your images</b></li>';
				$('.img-bank').html(no_img);
			}
		}
	});
}

/**
	Function loadFooterEffect to load header effects
**/
function loadFooterEffect(){
	if(jQuery('#footer').find('.footer_menu').length>0){
		jQuery('#footer').find('.footer_menu').remove();
	}
	$('#footer').prepend(handler_footer);
	var address=$('.address').html().replace(/&nbsp;/g, "");
	$('.address').html(address);
	$('.address').parent().css("margin-left","0px");
	$('.copyright').html('&copy; ');
}

function close_block(){
	$('#active_block').attr('id','');
	$('#active_header').attr('id','');
	$('#active_logo').attr('id','');
	$('#active_image_bank').attr('id','');
	$('#active_theme_color').attr('id','');
}
/**
*	Function toolboxDisplay to display toolbox blocks
*
**/
function toolboxDisplay(){
	$('.toolbox').addClass('selected');
	$('.imagebank').removeClass('selected');
	$('.color').removeClass('selected');
	$('#one').show();
	$('#two').hide();
	$('#three').hide();
}
/**
	Function imageBankDisplay to display image bank images for login user
**/
function imageBankDisplay(){
	$('.toolbox').removeClass('selected');		//Unhighlight toolbox tab
	$('.color').removeClass('selected');		//Unhighlight colorbox tab
	$('.imagebank').addClass('selected');		//Hightlight imagebank tab
	$('#one').hide();
	$('#two').show();
	$('#three').hide();
	if($('.load_images').length>0){
		jQuery.ajax({
			url: "<?php echo base_url() ?>newsletter/campaign/get_image_bank_for_ajax",
			type:"POST",
			success: function(data) {
				$('.img-bank').html(data);
				organizeImageBank();
				dragDropImageBank();
			}
		});
	} else {
		$('.img-bank').masonry('reload');
	}
	var delay = function() { setHeaderHeight(); };
	setTimeout(delay, 1000);
}

/**
	Function colorboxDisplay to display colors options
**/
function colorboxDisplay(){
	$('.colorstab li').removeClass('active');
	$($('.colorstab li').get(0)).addClass('active');
	$('.toolbox').removeClass('selected');
	$('.imagebank').removeClass('selected');
	$('.color').addClass('selected');
	$('#one').hide();
	$('#two').hide();
	$('#three').show();
	if($('.load_colors').length>0){
		jQuery.ajax({
			url: "<?php echo base_url() ?>newsletter/campaign/get_theme_colors",
			type:"POST",
			success: function(data) {
				$('#default_theme_color').html(data);
			}
		});
	}

	$('#custom-theme').slideUp();
	$('#default-theme').slideDown();
}

/**
	Function to save imagebank's images url
**/
function save_image_bank_url(){
	var image_url=$('#fancybox-wrap').find('#image_bank_url').attr('value');
	var image_data="image_url="+image_url;
	jQuery.ajax({
		url: "<?php echo base_url() ?>ajax/copy_image_bank_image_url",
		type:"POST",
		data:image_data,
		success: function(data) {
			var image_obj=jQuery.parseJSON(data);
			error=image_obj.error;
			if(error=="true"){
				$('#fancybox-wrap').find('.img_upload_msg').show();
				$('#fancybox-wrap').find('.img_upload_msg').html(image_obj.error_msg);
				$('#fancybox-wrap').find('.img_upload_msg').addClass('error');
			}else{
				$('#fancybox-wrap').find('.img_upload_msg').hide();
				var img_path=image_obj.file_path;
				var img_height=image_obj.height;
				var img_width=image_obj.width;
				var img_id=image_obj.img_id;
				var thumb_img_path=image_obj.thumb_image_path;
				var li_class_name=$('#fancybox-wrap').find(".img-bank  li:last-child").attr("class");
				var li_name=img_path+','+img_width+','+img_height;
				li_class_name='class="li_draggable" ';
				var img_remove='<div  class="del_image_link"><a href="javascript:void(0);"  class="remove-img-link image_bank_unlink" id="'+img_id+'"><i class="icon-remove"></i></a></div>';
				var img_html='<div  class="image_div"><img class="image_bank_div draggable1" src="'+img_path+'?a='+'block_'+new Date().getTime()+'" name="'+li_name+'"   /></div>';
				var thumb_img_html='<img  src="'+thumb_img_path+'"  />';
				parent.$('.load_images').remove();
				parent.$('.img-bank').append('<li '+li_class_name+'  title="Click & Drag"><div  class="img_slide" >'+img_remove+img_html+'</div></li>');
				$.fancybox.close();
			}
			dragDropImageBank();
		}
	});
}

/**
	Function to remove  color theme from colorbox
**/
function unlinkThemeColor(theme_id){
	jQuery.ajax({
		url: "<?php echo base_url() ?>ajax/unlink_theme_color/"+theme_id,
		type:"POST",
		success: function(data) {
			jQuery("#"+theme_id).remove();
		}
	});
}
/**
	Function changeStyle to change color or font style of email tempalte
**/
function changeStyle(element_id,color,element){
	$style="";
	if(element=="font_color"){
		$("."+element_id+"_font_style").remove();
		$style="<style class='footer_font_style custome_style'>#"+element_id+"{color:#"+color+" !important}</style>";
		$('#template_container').prepend($style);
	}else if(element=="font_size"){
		$("#"+element_id).css('font-size',$('#footer_font_size').val());
		$(".selected_font").css('font-size',$('#footer_font_size').val());
	}else if(element=="preheader"){
		$(".preheader-text").css('color','#'+color);		
		$(".preheader-link").css('color','#'+color);
		$(".preheader_font_color").remove();
		$style="<style class='footer_font_style custome_style'>#"+element_id+"{color:#"+color+" !important}</style>";
		$style='<style class="preheader_font_color custome_style">.preheader-text, .preheader-link { color: #'+ color + ';}</style>';
		$('#template_container').prepend($style);
	}else if(element=="border_style"){
		if(jQuery("#body-options-border").val()=="thin"){
			$("#"+element_id).css('border-width',$('#body-options-border').val());
			$("#"+element_id).css('border-style','solid');
			$("#template_container").width('597');
			$("#email_template_table").attr('width','597');
		}
		else if(jQuery("#body-options-border").val()=="thick"){
			$("#"+element_id).css('border-width',$('#body-options-border').val());
			$("#"+element_id).css('border-style','solid');
			$("#template_container").width('605');
			$("#email_template_table").attr('width','605');
		}else if(jQuery("#body-options-border").val()=="solid"){
			$("#"+element_id).css('border-width','2px');
			$("#"+element_id).css('border-style',$('#body-options-border').val());
			$("#template_container").width('599');
			$("#email_template_table").attr('width','599');
		}else if(jQuery("#body-options-border").val()=="none"){
			$("#"+element_id).css('border-style',$('#body-options-border').val());
			$("#template_container").width('595');
			$("#email_template_table").attr('width','595');
		}else{
			if($("#"+element_id).attr('width')=='595'){
				$("#"+element_id).css('border-style','solid');
				$("#"+element_id).css('border-width','2px');
				$("#template_container").width('599');
				$("#email_template_table").attr('width','599');
			}
			$("#"+element_id).css('border-style',$('#body-options-border').val());
		}
	}else if(element=="border"){
		$(".border_style").remove();
		$style="<style class='border_style custome_style'>#"+element_id+"{border-color:#"+color+" !important}.body_border{background-color:#"+color+" !important}</style>";
		$('#template_container').prepend($style);
	}else{
		$("."+element_id+"_style").remove();
		if(element_id=="main-table"){
			$style="<style class='"+element_id+"_style custome_style'>html, body{background-color:#"+color+" !important}.diy-editor{background-color:#"+color+" !important}#template_container{background-color:#"+color+" !important}.outer_bg{background-color:#"+color+" !important}</style>";
		}else if(element_id=="body_main"){
			$style="<style class='"+element_id+"_style custome_style'>#"+element_id+"{background-color:#"+color+" !important}.body_bg_color{background-color:#"+color+" !important}</style>";
		}else if(element_id=="footer"){
			$style="<style class='"+element_id+"_style custome_style'>#"+element_id+"{background-color:#"+color+" !important}.footer_txt_color{background-color:#"+color+" !important}</style>";
		}else{
			$style="<style class='"+element_id+"_style custome_style'>#"+element_id+"{background-color:#"+color+" !important}</style>";
		}
		$('#template_container').prepend($style);
	}
	pagechange=true;	//Page content change
}

/**
	Function to add theme color in databse
**/
function saveThemeColor(){
	var block_data="";
	block_data='color_theme_name='+$("#color_theme_name").val();
	block_data+='&theme_body_color='+hexc($("#background_color_txt").css('background-color')).replace("#","");
	block_data+='&theme_outer_bg_color='+hexc($("#background_outer_txt").css('background-color')).replace("#","");
	block_data+='&theme_preheader_color='+hexc($("#preheader_color").css('color')).replace("#","");
	block_data+='&theme_border_color='+hexc($("#border_txt").css('background-color')).replace("#","");
	block_data+='&theme_footer_color='+hexc($("#footer_txt").css('background-color')).replace("#","");
	 
	jQuery.ajax({
		url: "<?php echo base_url() ?>ajax/add_color_theme",
		type:"POST",
		data:block_data,
		success: function(data) {
			var data_arr=data.split(":");
			if(data_arr[0]=="error"){
				$('.theme_color_info').show();
				$('.theme_color_info').html(data_arr[1]);
				$('.theme_color_info').addClass('info');
			}else{
				$('.theme_color_info').show();
				$('.theme_color_info').html("Theme has been added");
				$('.theme_color_info').addClass('info');
				var theme_tr='<tr id="'+data_arr[1]+'" class="color_theme_link"><td>'+$("#color_theme_name").val().substr(0,15)+'<span class="close_link_span"><a  class="close-link theme_color_delete" id="theme_color_'+data_arr[1]+'" href="javascript:void(0);" ><img title="Delete"  src="<?php echo $this->config->item('webappassets');?>images/close.gif?v=6-20-13" /></a></span></td><td id="outer_bg_'+data_arr[1]+'" style="background:'+hexc($("#background_outer_txt").css('background-color'))+';width:7.5px;" class="color" class="outer_bg"  onclick="saveColorTheme(\''+data_arr[1]+'\',this)">&nbsp; </td><td id="body_bg_'+data_arr[1]+'"  style="background:'+hexc($("#background_color_txt").css('background-color'))+';width:7.5px;" class="body_bg"  onclick="saveColorTheme(\''+data_arr[1]+'\',this)">&nbsp;</td><td id="footer_bg_'+data_arr[1]+'"  style="background:'+hexc($("#footer_txt").css('background-color'))+';width:7.5px;" class="footer_bg" onclick="saveColorTheme(\''+data_arr[1]+'\',this)"> &nbsp;</td><td id="border_color_'+data_arr[1]+'"  style="background:'+hexc($("#border_txt").css('background-color'))+';width:7.5px;" class="border_color" onclick="saveColorTheme(\''+data_arr[1]+'\',this)"> &nbsp;</td></tr>';
				$('#default_theme_color').append(theme_tr);
				$("#color_theme_name").val("");
			}
			$(".color_theme_dialog").toggle();
			setTimeout( function(){$('.theme_color_info').fadeOut();} , 4000);
		}
	});
}
/**
	Function hexc to conver color from rgb() to # format
**/
function hexc(colorval) {
  var rgb = colorval;
  if (!rgb) return '#FFFFFF'; //default color
  var hex_rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
  function hex(x) {
  	return ("0" + parseInt(x).toString(16)).slice(-2);
  }
  if (hex_rgb) return "#" + hex(hex_rgb[1]) + hex(hex_rgb[2]) + hex(hex_rgb[3]);
  else return rgb; //ie8 returns background-color in hex format
}
/**
	Function resetDefault to reset the colors of email container
**/
function resetDefault(){
	page_id=jQuery("#current_tab_page").val();
	jQuery.ajax({
		url: "<?php echo base_url() ?>newsletter/page/resetColor/"+page_id+"/"+'block_'+new Date().getTime(),
		type:"POST",
		success: function(data) {

		}
	});
}

/**
	Function to add user info on footer
**/
function saveFooterText(){
	var block_data;
	block_data='company='+encodeURIComponent($('#company_name_footer').val())+'&address_line_1='+encodeURIComponent($('#address_footer').val())+'&city='+encodeURIComponent($('#city_footer').val())+'&state='+encodeURIComponent($('#state_footer').val())+'&zipcode='+encodeURIComponent($('#zip_footer').val())+'&country='+encodeURIComponent($('#country_name_footer').val())+'&country_custom='+encodeURIComponent($('#country_custom_name_footer').val());
	jQuery.ajax({
		url: "<?php echo base_url() ?>account/user_info",
		type:"POST",
		data:block_data,
		success: function(data){
			var data_arr=data.split(':');
			if(data_arr[0]=="error"){
				$('.msg').html(data_arr[1]);
			}else{
				$('.company_name').html('<b><span class="copyright">&copy; </span>'+$("#company_name_footer").val()+'</b>');
				$('.address').html($("#address_footer").val());
				$('.city').html(" | "+$("#city_footer").val());
				$('.state').html(", "+$("#state_footer").val());
				$('.zip').html($("#zip_footer").val());
				var country=$("#country_name_footer :selected").text();
				if(country=="United States"){
					country="USA";
					$('.country').html('');
				}else if(country=="Custom"){
					country=$("#country_custom_name_footer").val();
					$('.country').html(" | "+country);
				}else{
					$('.country').html(" | "+country);
				}
				jQuery("#footer_link_option").dialog("close");
				jQuery('.save_campaign').removeClass('disable-link');	// enable save link
			}
			pagechange=true;	//Page content change
		}
	});
}

/**
	Function to save the email content
**/
function publishPageContent(page_id,email_campaign_id,preview){
	$('#body_main').find('.ui-sortable-helper').remove();
	$('#body_main').find('.ui-draggable').remove();
	$('#body_main').find('.ui-state-highlight').remove();
	$('.diy_demo_video').remove();
	$('.gr-textarea-btn').remove();
	if(pagechange){
		var campaign_title=$('#campaign_title').val();
		if(campaign_title){
		// If campaign title is not empty then proceed to save the campaign
			$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
			var campaign_content=encodeURIComponent($('#template_container').html());
			var block_data="campaign_content="+campaign_content+"&campaign_title="+encodeURIComponent($('#campaign_title').val())+"&campaign_outer_bg="+encodeURIComponent($('.outer_bg').css('background-color'));

			var url="";
			if(campaign_text=="auotresponder"){
				url="<?php echo base_url() ?>newsletter/page/publish_content/"+page_id+"/"+email_campaign_id+"/1/"+'block_'+new Date().getTime();
			}else{
				url="<?php echo base_url() ?>newsletter/page/publish_content/"+page_id+"/"+email_campaign_id+"/0/"+'block_'+new Date().getTime();
			}

			jQuery.ajax({
				url: url,
				type:"POST",
				data:block_data,
				async: false,
				contentType: "application/x-www-form-urlencoded;charset=utf-8",
				success: function(data) {
					pagechange=false;

					if(preview==1){
						window.open($('#preview_alert').attr('href'),'_blank');
					}else if(preview==2){
						window.location=$('#next_step').attr('href');
					}
				}
			});
			$.unblockUI();
		}else{	// If campaign title is empty then display error message
			alert("Please Enter Campaign Name");
		}
	}
}

/**
	Save Color theme for email campaign
**/
saveColorTheme=function(template_id,obj){
	header_image_change=1;
	campaign_color_theme_id=template_id;
	var url="";
	<?php if($is_auotresponder){ ?>
		url="<?php echo base_url() ?>newsletter/autoresponder/change_theme/"+campaign_color_theme_id+"/"+email_campaign_id;
	<?php }else{ ?>
		url="<?php echo base_url() ?>newsletter/campaign/change_theme/"+campaign_color_theme_id+"/"+email_campaign_id;
	<?php } ?>
	jQuery.ajax({
		url: url,
		success: function(data) {
			var url="";
			<?php if($is_auotresponder){ ?>
				url="<?php echo base_url() ?>newsletter/autoresponder/get_theme_css/"+template_id+"/ajax";
			<?php }else{ ?>
				url="<?php echo base_url() ?>newsletter/campaign/get_theme_css/"+template_id+"/ajax";
			<?php } ?>
			jQuery.ajax({
				url: url,
				success: function(data) {
					$('.custome_style').remove();
					$('#template_container').prepend(data);
					resetDefault();
					pagechange=true;	//Page content change
				}
			});
		}
	});
	jQuery('.save_campaign').removeClass('disable-link');	// enable save link
}

function loadImageSize(element_id){
	var elements=$('#'+element_id).find('.image-container');
	var element_length=$('#'+element_id).find('.image-container').length;
	var element_image_text=$('#'+element_id).find('.text_image');
	var height=0;
	if(element_image_text.length==1){
		height=200;
	}else if(element_length==1){
		height=265;
	}else if(element_length==2){
		height=265;
	}else if(element_length==3){
		height=168;
	}else if(element_length==4){
		height=120;
	}
	var calc_width=0;
	var min_height=0;

	if(element_image_text.length==1){
		elements.each(function() {
			// Calcultate Minmum height
			calc_width+=$(this).attr('name')*height+25;
		});
		if(calc_width>225){
			var set_width=false;
			while(!set_width){
				height--;
				calc_width=0;
				elements.each(function() {
					// Calcultate Minmum height
					calc_width+=$(this).attr('name')*height+25;
				});
				if(calc_width<=225){
					set_width=true;
				}
			}
			min_height=height;
		}else{
			min_height=height;
		}
	}else{
		elements.each(function() {
			// Calcultate Minmum height
			calc_width+=$(this).attr('name')*height;
		});
		var comapre_width=element_length*height;
		if(calc_width>comapre_width){
			var set_width=false;
			while(!set_width){
				height--;
				calc_width=0;
				elements.each(function() {
					// Calcultate Minmum height
					calc_width+=$(this).attr('name')*height;
				});
				if(calc_width<=comapre_width){
					set_width=true;
				}
			}
			min_height=height;
		}else{
			min_height=height;
		}
	}
	var i=1;
	elements.each(function() {
		//set Width and height to each image
		$(this).attr('width',min_height*$(this).attr('name'));
		$(this).attr('height',min_height);
		$(this).parent().find('.resize_div').width(min_height*$(this).attr('name'));
		$(this).parent().parent().find('.active_image_option').width(min_height*$(this).attr('name'));
		$(this).parent().find('.resize_div').height(min_height);
		$(this).parent().find('.resize_div_text').width(min_height*$(this).attr('name'));
		$(this).parent().find('.resize_div_text').height(min_height);
		$(this).parents('.container-div').find('.resize_table').width(calc_width);

		if(i<element_length){
			$(this).parent().css('padding-right','25px');
		}else{
			$(this).parent().css('padding-right','0px');
		}



		i++;
	});
	// ----------------------
	//alert($('#'+element_id).children('div.text-paragraph-container').css('min-height'));
	//$('#'+element_id).next('#div_'+element_id).css('min-height',(min_height+7)+'px');
	$('#'+element_id).find('div.text-paragraph-container').css('min-height',(min_height+7)+'px');
	// ----------------------

}
/**
	Function imageResize to resize images
**/
function imageResize(element_id){
	// Delete Resize classes
	if(element_id){
		$('#'+element_id).find('.resize_div').removeClass('ui-resizable');
		$('#'+element_id).find('.ui-resizable-handle').remove();
	}else{
		$('.resize_div').removeClass('ui-resizable');
		$('.resize_div').find('.ui-resizable-handle').remove();
		$('.resize_div_text').removeClass('ui-resizable');
		$('.resize_div_text').find('.ui-resizable-handle').remove();
	}
	/**
		Resize Image Block
	**/
	$('.resize_div').resizable({
		handles:'se',
		helper:'ui-state-helper',
		minWidth:70,
		minHeight:70,
		aspectRatio:true,
		resize:function(event,ui)
		{
			jQuery('.handler_img').hide();
			jQuery('.handler').hide();
			jQuery('.container-div').removeClass('highlighted');
			jQuery('.div_border').hide();
			jQuery('.ui-resizable-handle').hide();
			/**
				calculate width and height of image block during resize event
			**/
			var parent_obj=jQuery(this).parents('.container-div');
			var width=jQuery(ui.helper).width();
			var height=jQuery(ui.helper).height();
			var elements=parent_obj.find('.image-container');
			var element_length=parent_obj.find('.image-container').length;
			var calc_height=0;
			if(element_length==1){
				calc_height=555;
			}else if(element_length==2){
				calc_height=265;
			}else if(element_length==3){
				calc_height=168;
			}else if(element_length==4){
				calc_height=120;
			}
			var calc_width=0;
			var min_height=0;
			elements.each(function() {
				// Calcultate Minmum height
				calc_width+=$(this).attr('name')*height;
			});
			var comapre_width=element_length*calc_height;
			if(calc_width>comapre_width){
				var set_width=false;
				while(!set_width){
					height--;
					calc_width=0;
					elements.each(function() {
						// Calcultate Minmum height
						calc_width+=$(this).attr('name')*height;
					});
					if(calc_width<=comapre_width){
						set_width=true;
					}
				}
				min_height=height;
			}else{
				min_height=height;
			}
			/**
				calculate gap between images
			**/
			var gap=0;
			if(element_length>1){
				gap=25*(element_length-1);
			}
			var parent_obj_id=parent_obj.attr('id');

			jQuery('#'+parent_obj_id+"_1").find('.handler_img_width_height').html(parseInt(calc_width+gap)+"X"+min_height);
			jQuery('#'+parent_obj_id+"_1").find('.handler_img_width_height').show();

			$('#'+parent_obj_id).find('.video_play').css({'top':(min_height-41)/2+'px','left':(calc_width-58)/2+'px'});
		},
		stop:function(event,ui)
		{
			var parent_obj=jQuery(this).parents('.container-div');
			var width=jQuery(ui.helper).width();
			var height=jQuery(ui.helper).height();
			var elements=parent_obj.find('.image-container');
			var element_length=parent_obj.find('.image-container').length;
			var calc_height=0;
			if(element_length==1){
				calc_height=555;
			}else if(element_length==2){
				calc_height=265;
			}else if(element_length==3){
				calc_height=168;
			}else if(element_length==4){
				calc_height=120;
			}
			var calc_width=0;
			var min_height=0;
			elements.each(function() {
				// Calcultate Minimum height
				calc_width+=$(this).attr('name')*height;
			});
			var comapre_width=element_length*calc_height;
			if(calc_width>comapre_width){
				var set_width=false;
				while(!set_width){
					height--;
					calc_width=0;
					elements.each(function() {
						// Calcultate Minmum height
						calc_width+=$(this).attr('name')*height;
					});
					if(calc_width<=comapre_width){
						set_width=true;
					}
				}
				min_height=height;
			}else{
				min_height=height;
			}
			var i=1;
			elements.each(function() {
				//set Width and height to each image
				$(this).attr('width',min_height*$(this).attr('name'));
				$(this).attr('height',min_height);
				$(this).parent().find('.resize_div').width(min_height*$(this).attr('name'));
				$(this).parents('.container-div').find('.resize_table').width(calc_width);
				$(this).parent().parent().find('.active_image_option').width(min_height*$(this).attr('name'));
				$(this).parent().find('.resize_div').height(min_height);
				if(i<element_length){
					$(this).parent().css('padding-right','25px');
				}else{
					$(this).parent().css('padding-right','0px');
				}
				i++;
			});
			jQuery('.div_border').show();
			var parent_obj_id=parent_obj.attr('id');
			jQuery('#'+parent_obj_id+"_1").find('.handler_img_width_height').hide();
			pagechange=true;	//Page content change
		}
	});

	/**
		Resize Image Text Block
	**/
	$('.resize_div_text').resizable({
		handles:'se',
		helper:'ui-state-helper',
		minWidth:77,
		minHeight:77,
		aspectRatio:true,
		resize:function(event,ui)
		{
			jQuery('.handler_img').hide();
			jQuery('.handler').hide();
			jQuery('.container-div').removeClass('highlighted');
			jQuery('.div_border').hide();
			jQuery('.ui-resizable-handle').hide();
			jQuery(this).find('.handler_img_width_height').show();
			var parent_obj=jQuery(this).parents('.container-div');
			var width=jQuery(ui.helper).width();
			var height=jQuery(ui.helper).height();
			var elements=parent_obj.find('.image-container');
			var calc_width=0;
			var min_height=0;
			// Calcultate Minmum height
			elements.each(function() {
				calc_width+=$(this).attr('name')*height+25;
			});
			if(calc_width>375){
				var set_width=false;
				while(!set_width){
					height--;
					calc_width=0;
					elements.each(function() {
						// Calcultate Minmum height
						calc_width+=$(this).attr('name')*height+25;
					});
					if(calc_width<=375){
						set_width=true;
					}
				}
				min_height=height;
			}else{
				min_height=height;
			}
			jQuery(this).find('.handler_img_width_height').html(parseInt(calc_width-25)+"X"+min_height);
		},
		stop:function(event,ui)
		{
			var parent_obj=jQuery(this).parents('.container-div');
			var width=jQuery(ui.helper).width();
			var height=jQuery(ui.helper).height();
			var elements=parent_obj.find('.image-container');
			var calc_width=0;
			var min_height=0;
			// Calcultate Minmum height
			elements.each(function() {
				calc_width+=$(this).attr('name')*height+25;
			});
			if(calc_width>375){
				var set_width=false;
				while(!set_width){
					height--;
					calc_width=0;
					elements.each(function() {
						// Calcultate Minmum height
						calc_width+=$(this).attr('name')*height+25;
					});
					if(calc_width<=375){
						set_width=true;
					}
				}
				min_height=height;
			}else{
				min_height=height;
			}
			//set Width and height to each image
			elements.each(function() {
				$(this).attr('width',min_height*$(this).attr('name'));
				$(this).attr('height',min_height);
				$(this).parent().find('.resize_div_text').width(min_height*$(this).attr('name'));
				$(this).parent().parent().find('.active_image_option').width(min_height*$(this).attr('name'));
				$(this).parent().find('.resize_div_text').height(min_height);
			});
			// ----------------------
			var container_min_height =(parent_obj.find('.text_img_content').height());
			parent_obj.find('.text-paragraph-container').css('min-height',container_min_height+'px');
			// ----------------------
			parent_obj.find('.resize_table').width(calc_width);
			jQuery('.div_border').show();
			jQuery(this).find('.handler_img_width_height').hide();
			pagechange=true;	//Page content change
		}
	});
}
function loadOfferEffects(element_id){
	if(!element_id){
		$('.edit_offer_div').removeClass('ui-resizable');
		$('.edit_offer_div').find('.ui-resizable-handle').remove();
	}
	var elements=jQuery('.edit_offer_div');
	elements.each(function() {
		jQuery(this).resizable({
			aspectRatio:false,
			minWidth:150,
			minHeight:150,
			maxWidth:543,
			handles: 'e',
			resize:function(event,ui)
			{
				var width=jQuery(ui.helper).width()+12;
				var height=jQuery(ui.helper).height();
				jQuery(this).find('.handler_img_width_height').html(parseInt(width)+"X"+height);
				jQuery(this).find('.handler_img_width_height').show();
			},
			stop:function(event,ui)
			{
				jQuery(ui.helper).css('height','auto');
				jQuery(ui.helper).css('min-height','150px');
				jQuery('.save_campaign').removeClass('disable-link');	// enable save link
				jQuery(this).find('.handler_img_width_height').hide();
			}
		});
	});
}
/**
	Function loadImageEffects to load image effects like  add highlight_on_image_hover border
**/
function loadImageEffects(){
	$('.highlight_on_image_hover').remove();
	$('.ui-resizable-handle').hide();
	$('.div_border').after("<div  class=\'highlight_on_image_hover\'><span style='display:none;' class='drop_div_border'></span></div>");
	$('.div_border').css('border','none');
}
/**
	Function loadLogoEffect to load logo effects:draggable,resize logo
**/
function loadLogoEffect(){
	$('.logo-resize-div').removeClass('ui-resizable');
	$('#logo').removeClass('ui-draggable');
	$('.logo-resize-div').find('.ui-resizable-handle').remove();
	jQuery('#logo').draggable({
		zIndex: 	1000,
		ghosting:	false,
		opacity: 	0.7,
		containment : '#header',
		stop: function(event, ui) {
			pagechange=true;	//Page content change
		}
	});
	jQuery( "#header" ).droppable({
	'tolerance':'touch',
	accept:"#logo",
		drop: function( event, ui ) {
			pagechange=true;	//Page content change
		}
	});
	jQuery('#logo').find('.logo-resize-div').resizable({
		aspectRatio:true,
		minWidth:50,
		minHeight:50,
		containment: '#header',
		handles:'se',
		resize: function(event,ui){
			var height=jQuery(ui.helper).width();
			var width=jQuery(ui.helper).width();
			jQuery(this).parent().find('.handler_img_width_height').html(parseInt(width)+"X"+height);
			jQuery(this).parent().find('.handler_img_width_height').show();
		},
		stop:function(event,ui){
			var aspect_ratio=jQuery('.logo_img').find('img').width()/jQuery('.logo_img').find('img').height();
			var height=jQuery(ui.helper).width()/aspect_ratio;
			jQuery('.logo_img').find('img').width(jQuery(ui.helper).width());
			jQuery('.logo_img').find('img').height(height);
			jQuery('#logo').width(jQuery(ui.helper).width());
			jQuery(ui.helper).height(height);
			jQuery(this).parent().find('.handler_img_width_height').hide();
			pagechange=true;	//Page content change
		}
	});
	dragDropImageBank();
}

function webCompatibleString(){
	var campaign_content=escape($('#template_container').html());
	campaign_content=campaign_content.replace(/%u201C/g, "%22");
	campaign_content=campaign_content.replace(/%u201D/g, "%22");
	campaign_content=campaign_content.replace(/%u2018/g, "%27");
	campaign_content=campaign_content.replace(/%u2019/g, "%27");
	campaign_content=campaign_content.replace(/%u2026/g, "...");
	campaign_content=campaign_content.replace(/%u2014/g, "-");
	campaign_content=campaign_content.replace(/%u2013/g, "-");
	campaign_content=campaign_content.replace(/%u2022/g, ".");
	return campaign_content;
}
function customColors(){ 
	$('#background_color_txt').val(hexc($('.body_bg_color').css('background-color')));
	$('#background_color_txt').css("color",hexc($('.body_bg_color').css('background-color')));
	$('#background_outer_txt').val(hexc($('.outer_bg').css('background-color')));
	$('#background_outer_txt').css("color",hexc($('.outer_bg').css('background-color')));
	$('#preheader_color').val(hexc($('.preheader_color').css('background-color')));
	$('#preheader_color').css("color",hexc($('.preheader_color').css('background-color')));
	$('#footer_txt').val(hexc($('.footer_txt_color').css('background-color')));
	$('#footer_txt').css("color",hexc($('.footer_txt_color').css('background-color')));
	$('#border_txt').val(hexc($('.body_border').css('background-color')));
	$('#border_txt').css("color",hexc($('.body_border').css('background-color')));
	/* $('#footer_color_txt').val(hexc($('.footer_color_txt').css('background-color')));
	$('#footer_color_txt').css("color",hexc($('.footer_color_txt').css('background-color'))); */
	$('#custom-theme').slideDown();
	$('#default-theme').slideUp();
}

/**
	show border on mouse over of block
**/
jQuery(".container-div")
	.live('mouseover',function(){
		jQuery(this).addClass('highlighted');
		jQuery(this).find('.handler').show();
		jQuery(this).find('.resize_div').show();
		jQuery(this).find('.resize_div_text').show();
})
	.live('mouseout',function(){
		jQuery(this).find('.handler').hide();
		jQuery(this).removeClass('highlighted');
});
jQuery(".offer")
	.live('mouseover',function(){
		jQuery(this).find('.ui-resizable-handle').show();	//Show offer block resize icon
})
	.live('mouseout',function(){
		jQuery(this).find('.ui-resizable-handle').hide();		//Hide offer block resize icon
});
jQuery(".img_content")
	.live('mouseover',function(){
		jQuery(this).find('.ui-resizable-handle').show();	//Show offer block resize icon
})
	.live('mouseout',function(){
		jQuery(this).find('.ui-resizable-handle').hide();		//Hide offer block resize icon
		jQuery(this).find('.div_border').css('border',"none");
});
jQuery(".position_div")
	.live('mouseover',function(){
		jQuery(this).find('.handler_img').show();
})
	.live('mouseout',function(){
		jQuery(this).find('.handler_img').hide();
});
/**
	show toolbar on mouse over of header
**/
jQuery("#header")
	.live('mouseover',function(){
		jQuery(this).find('.menu').show();
})
	.live('mouseout',function(){
		jQuery(this).find('.menu').hide();
});
/**
	Show twitter text box on check of twitter link
**/
$("#twitter_link").live('change',function(){
	// your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#twitter_url').show();
		$('#fancybox-wrap').find('#add_twitter').hide();
	}else{
		$('#fancybox-wrap').find('#twitter_url').hide();
		$('#fancybox-wrap').find('#add_twitter').show();
	}
});
/**
	Show facebook text box on check of facebook link
**/
$("#facebook_link").live('change',function(){
   // your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#facebox_url').show();
		$('#fancybox-wrap').find('#add_facebook').hide();
	}else{
		$('#fancybox-wrap').find('#facebox_url').hide();
		$('#fancybox-wrap').find('#add_facebook').show();
	}
});
/**
	Show linkedin text box on check of linkedin link
**/
$("#linkedin_link").live('change',function(){
	// your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#linkedin_url').show();
		$('#fancybox-wrap').find('#add_linkedin').hide();
	}else{
		$('#fancybox-wrap').find('#linkedin_url').hide();
		$('#fancybox-wrap').find('#add_linkedin').show();
	}
});
/**
	Show rss feed text box on check of ress feed link
**/
$("#rss_link").live('change',function(){
	// your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#rss_url').show();
		$('#fancybox-wrap').find('#add_rss').hide();
	}else{
		$('#fancybox-wrap').find('#rss_url').hide();
		$('#fancybox-wrap').find('#add_rss').show();
	}
});
/**
	Show youtube text box on check of youtube link
**/
$("#youtube_link").live('change',function(){
   // your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#youtube_url').show();
		$('#fancybox-wrap').find('#add_youtube').hide();
	}else{
		$('#fancybox-wrap').find('#youtube_url').hide();
		$('#fancybox-wrap').find('#add_youtube').show();
	}
});
/**
	Show google plus text box on check of google plus link
**/
$("#google_plus_link").live('change',function(){
   // your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#google_plus_url').show();
		$('#fancybox-wrap').find('#add_google_plus').hide();
	}else{
		$('#fancybox-wrap').find('#google_plus_url').hide();
		$('#fancybox-wrap').find('#add_google_plus').show();
	}
});
/**
	Show tumblr text box on check of tumblr link
**/
$("#tumblr_link").live('change',function(){
   // your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#tumblr_url').show();
		$('#fancybox-wrap').find('#add_tumblr').hide();
	}else{
		$('#fancybox-wrap').find('#tumblr_url').hide();
		$('#fancybox-wrap').find('#add_tumblr').show();
	}
});
/**
	Show flickr text box on check of flickr link
**/
$("#flickr_link").live('change',function(){
   // your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#flickr_url').show();
		$('#fancybox-wrap').find('#add_flickr').hide();
	}else{
		$('#fancybox-wrap').find('#flickr_url').hide();
		$('#fancybox-wrap').find('#add_flickr').show();
	}
});
/**
	Show skype text box on check of skype link
**/
$("#skype_link").live('change',function(){
   // your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#skype_url').show();
		$('#fancybox-wrap').find('#add_skype').hide();
	}else{
		$('#fancybox-wrap').find('#skype_url').hide();
		$('#fancybox-wrap').find('#add_skype').show();
	}
});
/**
	Show pinterest text box on check of pinterest link
**/
$("#pinterest_link").live('change',function(){
   // your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#pinterest_url').show();
		$('#fancybox-wrap').find('#add_pinterest').hide();
	}else{
		$('#fancybox-wrap').find('#pinterest_url').hide();
		$('#fancybox-wrap').find('#add_pinterest').show();
	}
});
/**
	Show instagram text box on check of instagram link
**/
$("#instagram_link").live('change',function(){
   // your code here
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#instagram_url').show();
		$('#fancybox-wrap').find('#add_instagram').hide();
	}else{
		$('#fancybox-wrap').find('#instagram_url').hide();
		$('#fancybox-wrap').find('#add_instagram').show();
	}
});
/**
	Show mailto text box on check of mailto link
**/
$("#mailto_link").live('change',function(){
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#mailto_url').show();
		$('#fancybox-wrap').find('#add_mailto').hide();
	}else{
		$('#fancybox-wrap').find('#mailto_url').hide();
		$('#fancybox-wrap').find('#add_mailto').show();
	}
});
/**
	Show website text box on check of website link
**/
$("#website_link").live('change',function(){
	if($(this).is(':checked')){
		$('#fancybox-wrap').find('#website_url').show();
		$('#fancybox-wrap').find('#add_website').hide();
	}else{
		$('#fancybox-wrap').find('#website_url').hide();
		$('#fancybox-wrap').find('#add_website').show();
	}
});
/**
	Remove block from email container
**/
jQuery(".close-link")
	.live('click',function(event){
		var confirm_msg=$("#confirm_msg").html();
		if(jQuery(this).hasClass('logo_class')){
			$(this).parents('#logo').find('.logo_img').attr('id','active_logo');
			confirm_msg=confirm_msg.replace('block','logo');
		}else if(jQuery(this).hasClass('theme_color_delete')){
			$(this).parent().parent('td').attr('id','active_theme_color');
			confirm_msg=confirm_msg.replace('block','theme');
		}else if(jQuery(this).hasClass('image_bank_unlink')){
			$(this).parent('div').attr('id','active_image_bank');
		}else if(jQuery(this).hasClass('header_unlink')){
			$(this).parents('#header').find('.header_img').attr('id','active_header');
			confirm_msg=confirm_msg.replace('block','banner');
		}else{
			jQuery(this).parents('.container-div').attr('id','active_block');
		}
		$.fancybox(confirm_msg,{'autoDimensions':false,'height':'169','width':'330','centerOnScroll':true,'onClosed':function() {close_block();}});
	});
jQuery(".remove-img-link")
	.live('click',function(event){
		var confirm_msg=$("#confirm_msg_img_remove").html();
		 if(jQuery(this).hasClass('image_bank_unlink')){
			$(this).parent('div').attr('id','active_image_bank');
		}
		$.fancybox(confirm_msg,{'autoDimensions':false,'height':'169','width':'330','centerOnScroll':true,'onClosed':function() {close_block();}});
	});
$('#fancybox-wrap').find(".delete-block")
	.live('click',function(event){
		if($('#active_logo').length>0){
			$('#logo').remove();
		}else if($('#active_image_bank').length>0){
			var image_id=$('#active_image_bank').find('.remove-img-link').attr('id');
			unlinkImageBank(image_id);
		}else if($('#active_theme_color').length>0){
			var theme_id=$('#active_theme_color').parent().attr('id');
			unlinkThemeColor(theme_id);
		}else if($('#active_header').length>0){
			$('.header_div').append('<span class="empty_header"><img src="<?php echo $this->config->item('webappassets');?>images/drop-header.png?v=6-20-13" width="595"/></span>');
			var delay = function() { setHeaderHeight(); };
			setTimeout(delay, 1000);
			$('#active_header').remove();
			$('.header_link').css('display','none');
			$('.header_link').parent().remove();
			$('.add_logo').parent().remove();
			$('.add_logo').css('display','none');
			$('#logo').remove();
			$('#header_link').remove();
		}else{
			$('#active_block').remove();
			if($('#body_main').find('table').length==0){
				$('#body_main').addClass('empty_block');
				$('#body_main').append(el_diy_demo_video);
			}
		}
		$.fancybox.close();
		pagechange=true;	//Page content change
		setTimeout(function() {
			$('.img-bank').masonry('reload');
		},100);
	});
$('#fancybox-wrap').find(".cancel_delete-link")
	.live('click',function(event){
	$('#active_block').attr('id','');
	$('#active_header').attr('id','');
	$('#active_logo').attr('id','');
	$('#active_image_bank').attr('id','');
	$('#active_theme_color').attr('id','');
	$.fancybox.close();
});

/**
	crate clone of image
**/
$('.clone_image').live('click',function(event){
	var parent_obj_id=$(this).parents('.container-div').attr('id');
	var img_length=$('#'+parent_obj_id).find('.img_content').length;
	var img_id=$(this).parents('.img_content').attr('id');
	var img_id_arr=img_id.split('_');
	var clone_img_id=img_id_arr[1]+1;
	$(this).parents('.img_content').parent().append($(this).parents('.img_content').clone());
	var img_container=$(this).parents('.img_content').parent().find('.img_content');
	var img_container_length=$(this).parents('.img_content').parent().find('.img_content').length;
	$('#'+parent_obj_id).find('.handler').parent().attr('colspan',img_container_length);
	var i=1;
	img_container.each(function() {
		$(this).attr('id',img_id_arr[0]+"_"+img_id_arr[1]+"_"+i);
		if(i==4){
			$(this).find('.image-container').attr('src','<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13');
			$(this).find('.image-container').attr('name','1');
			$(this).find('.image_caption').html('');
			$(this).find('.image_link').attr('title','');
			$(this).find('.image_link').hide();
			$(this).find('.handler_img').remove();
			$(this).find('.handler_img_width_height').remove();
			$(this).find('.resize_div').prepend(handler_image4);
			imageResize(img_id_arr[0]+"_"+img_id_arr[1]+"_4");
		}else if(img_container_length==i){
			$(this).find('.image-container').attr('src','<?php echo base_url() ?>webappassets/images/drop-img.jpg?v=6-20-13');
			$(this).find('.image-container').attr('name','1');
			$(this).find('.image_caption').html('');
			$(this).find('.image_link').attr('title','');
			$(this).find('.image_link').hide();
			$(this).find('.handler_img').remove();
			$(this).find('.handler_img_width_height').remove();
			$(this).find('.resize_div').prepend(handler_image2);
			imageResize(img_id_arr[0]+"_"+img_id_arr[1]+"_"+i);
		}else{
			$(this).find('.handler_img').remove();
			$(this).find('.handler_img_width_height').remove();
			$(this).find('.resize_div').prepend(handler_image4);
		}
		i++;
	});
	loadImageSize(parent_obj_id);
	dragDropImageBank();
	pagechange=true;	//Page content change
});
/**
	Delete clone of image
**/
$('.close-clone-link').live('click',function(event){
	var parent_obj_id=$(this).parents('.container-div').attr('id');
	$(this).parents('.img_content').remove();
	var img_container=$('#'+parent_obj_id).find('.img_content');
	var img_container_length=$('#'+parent_obj_id).find('.img_content').length;
	$('#'+parent_obj_id).find('.handler').parent().attr('colspan',img_container_length);
	var i=1;
	img_container.each(function() {
		if(img_container_length>=i){
			$(this).attr('id',parent_obj_id+"_"+i);
			if(img_container_length==1){
				$(this).find('.handler_img').remove();
				$(this).find('.handler_img_width_height').remove();
				$(this).find('.resize_div').prepend(handler_image1);
			}else if(i==4){
				$(this).find('.handler_img').remove();
				$(this).find('.handler_img_width_height').remove();
				$(this).find('.resize_div').prepend(handler_image4);
			}else if(img_container_length==i){
				$(this).find('.handler_img').remove();
				$(this).find('.handler_img_width_height').remove();
				$(this).find('.resize_div').prepend(handler_image2);
			}else{
				$(this).find('.handler_img').remove();
				$(this).find('.handler_img_width_height').remove();
				$(this).find('.resize_div').prepend(handler_image4);
			}
		}
		i++;
	});
	loadImageSize(parent_obj_id);
	pagechange=true;	//Page content change
});
/**
	open popup for image caption
**/
jQuery(".option_image-caption").live('click',function(){
	jQuery(this).attr('id','active_block');
	var image_caption=jQuery(this).parents('.img_content').find('.image_caption').html();
	$.fancybox($("#image_caption").html(),{'autoDimensions':false,'height':'263','width':'350','centerOnScroll':true});
	$('#fancybox-wrap').find("textarea#image_link_caption").val(image_caption);	//set caption in textarea
});

/**
	Open dialog box for  image options
**/
jQuery(".option_image-link").live('click',function(){
	jQuery(this).attr('id','active_block');
	$.fancybox($("#image_option").html(),{'autoDimensions':false,'height':'159','width':'300','centerOnScroll':true});
	$('#fancybox-wrap').find("#image_link").val(jQuery(this).parents('.img_content').find('.image_link').attr('name'));
});

/**
	Display Popup For Selct Theme
**/
jQuery(".select_theme").live('click',function(){
	$.ajax({
        type        : "POST",
        cache       : false,
        url         : base_url+'newsletter/campaign/get_theme_data_for_ajax',
        data        : $(this).serializeArray(),
        success: function(data) {
			if(data){
				$.fancybox(data,{'autoDimensions':false,'height':'405','width':'680','centerOnScroll':true});
			}
        }
    });
    return false;
});

/**
	Open popup  for  header link
**/
jQuery(".header_link").live('click',function(){
	jQuery("#header_link_option").dialog("open");
	$.fancybox($("#header_link_option").html(),{'autoDimensions':false,'height':'160','width':'280','centerOnScroll':true});
	$('#fancybox-wrap').find("#header_link_text").attr("value",jQuery('#header_link').find('img').attr('name'));
	jQuery("#header_link_option").parent().focus();
});
/**
	Open popup for logo
**/
jQuery(".add_logo").live('click',function(){
	$.fancybox($("#logo_dialog").html(),{'autoDimensions':true,'height':'auto','width':'350','centerOnScroll':true});
	jQuery("#logo_file").attr("value",'');
	jQuery("#logo_dialog").parent().focus();
});

/**
	Upload Logo
**/
jQuery('#logo_file').live('change',function(){
	$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
	$('#logo').remove();
	if(campaign_text == "auotresponder") var ctyp = 'a'; else var ctyp = 'c';
    jQuery(this).upload(base_url+'ajax/upload_diy_logo/'+ctyp+'/'+email_campaign_id, function(res) {
		var image_obj=res;
		var img_path=image_obj.file_path;
		var logo=logo_header.replace('[logo_src]',img_path+'?a='+'block_'+new Date().getTime());
		$('.header_div').prepend(logo);
		$.fancybox.close();
		loadLogoEffect();
    }, 'json');
	$.unblockUI();
});

/**
	Open dialog box for uploading  image bank
**/
jQuery(".upload_image_bank").live('click',function(){
	$.ajax({
        type        : "POST",
        cache       : false,
        url         : base_url+'newsletter/campaign/getImageBankSize'  ,
        success: function(data) {
			if(data == 'ok'){
				if(jQuery(this).find('img').length==0){
					$.fancybox($("#upload_image_bank_dialog").html(),{'autoDimensions':false,'height':'175','width':'340','centerOnScroll':true,'title':'Get Images'});
					$('.uplaod_image_url').show();
				}
			}else{
				$.fancybox($("#block_upload_image_bank_dialog").html(),{'autoDimensions':false,'height':'175','width':'340','centerOnScroll':true,'title':'Quota Exceeded'});
			}
        }
    });

});

/**
	upload image on change event of image file
**/
$('#fancybox-wrap').find('#image_bank_file').live('change',function(){
$('#fancybox-wrap').find('.img_upload_msg').show();
$('#fancybox-wrap').find('.img_upload_msg').html("Please wait...");
$('#fancybox-wrap').find('.img_upload_msg').addClass('error');
	$('.uplaod_image_url').hide();
    jQuery(this).upload(base_url+'ajax/upload_image_bank_image', function(res) {

		var image_obj=jQuery.parseJSON(res);
		error=image_obj.error;
		if(error=="true"){
			$('#fancybox-wrap').find('.img_upload_msg').show();
			$('#fancybox-wrap').find('.img_upload_msg').html(image_obj.error_msg);
			$('#fancybox-wrap').find('.img_upload_msg').addClass('error');
		}else{
			$('#fancybox-wrap').find('.img_upload_msg').hide();
			var img_path=image_obj.file_path;
			var img_height=image_obj.height;
			var img_width=image_obj.width;
			var img_id=image_obj.img_id;
			var thumb_img_path=image_obj.thumb_image_path;
			var li_class_name=parent.jQuery(".img-bank  li:last-child").attr("class");
			var li_name=img_path+','+img_width+','+img_height;
			li_class_name='class="li_draggable" ';
			var img_remove='<div class="del_image_link"><a href="javascript:void(0);"  class="remove-img-link image_bank_unlink" id="'+img_id+'"><i class="icon-remove-sign"></i></a></div>';
			var img_html='<div class="image_bank_div"><img class="image_bank draggable1" src="'+img_path+'?a='+'block_'+new Date().getTime()+'" name="'+li_name+'" style="width:100px" /></div>';
			var thumb_img_html='<img src="'+thumb_img_path+'"  >';
			parent.$('.load_images').remove();
			parent.jQuery('.img-bank').append('<li '+li_class_name+' title="Click & Drag"><div  class="img_slide" >'+img_remove+img_html+'</div></li>');
			$.fancybox.close();
			dragDropImageBank();
			setTimeout(function() {
				$('.img-bank').masonry('reload');
			},0);
		}
    }, 'text');
});

/**
	Slider to increase  width of image bank images
**/
$(function(){
	$( "#slider" ).slider({
		value:70,
		min: 70,
		max: 162,
		step: 1,
		slide: function( event, ui) {
			$('.img_slide').css({'width':ui.value+'px','min-height':ui.value+'px'});
			$('.li_draggable').css({'width':ui.value+'px','min-height':ui.value+'px'});
			$('.image_bank').css({'width':(ui.value)+'px','height':'auto'});
			$('.image_div').css({'width':ui.value+'px','height':'auto'});
		}
	});
});
/**
	Display toolbar on mouseover of imagebank images
**/
$(".li_draggable").live('mouseover',function(){
  $(this).find('.image_bank_div').addClass('thin-border-radius');
  $(".del_image_link",this).show();
}).live('mouseout',function(){
	$(this).find('.image_bank_div').removeClass('thin-border-radius');
    $(".del_image_link",this).hide();
});

/**
	Display toolbar on mouseover of colorbox
**/
jQuery(".color_theme_link").live('mouseover',function(){
	jQuery(this).find('.theme_color_delete').show();
}).live('mouseout',function(){
	jQuery(this).find('.theme_color_delete').hide();
});

/**
	Display Toolbar for footer on mouseover of footer content
**/
$("#footer").live('mouseover',function(){
	jQuery(this).find('.footer_menu').show();
}).live('mouseout',function(){
	jQuery(this).find('.footer_menu').hide();
});

/**
	Edit Footer Content
**/

jQuery(".edit_footer").live('click',function(){
	if('245' != $('#country_name_footer').val())
	$('span#country_custom_div').hide();
	else
	$('span#country_custom_div').show();

	jQuery("#footer_link_option").dialog("open");
	$('.selected_font').css('font-size',$('#footer').css('font-size'));
	$('#footer_font_size').val($('#footer').css('font-size'));
	$('.selected_font').css('color',$('#footer').css('color'));
	$('#footer_color_txt').css("background-color",hexc($('#footer').css('color')));
	$('#footer_color_txt').val(hexc($('#footer').css('color')));
	$('#footer_color_txt').css("color",hexc($('#footer').css('color')));
});
function showCustom(dpdCountry){
	if('245' == dpdCountry.value){
	$('span#country_custom_div').show();
	}else{
	$('span#country_custom_div').hide();
	}
}
/**
	Save email campaign on click of save link
**/
jQuery("a.save_campaign_changes").live('click',function(){
	var current_tab_page=jQuery("#current_tab_page").attr('value');
	if($(this).attr('id')=="next_step"){
		pagechange = true;
		publishPageContent(current_tab_page,email_campaign_id,2);
	}else{
		publishPageContent(current_tab_page,email_campaign_id);
	}
});
/**
	Onclick of enter button
**/
jQuery('#campaign_title').live("keydown", function(e) {
	var code = e.keyCode || e.which;
	if(code == 13) {
		if(jQuery(this).val()==""){
			jQuery(this).val("Unnamed");
		}
		jQuery("a.save_campaign_changes").click();
	}
});
/**
	Display Popup on click of privew link
**/
jQuery('#preview_alert').live('click',function(){
	if(pagechange){
		$.fancybox($('#preview_msg').html(),{'autoDimensions':false,'height':'205','width':'400','centerOnScroll':true});
		return false;
	}else{
		window.open($('#preview_alert').attr('href'),'_blank');
		return false;
	}
});
/**
	Save campaign changes
**/
$('#fancybox-wrap').find(".save_campaign_changes").live('click',function(event){
	//set Page content change
	var current_tab_page=jQuery("#current_tab_page").attr('value');
	publishPageContent(current_tab_page,email_campaign_id,1);
	$.fancybox.close();
});
/**
	Discard campaign changes
**/
$('#fancybox-wrap').find(".discard_campaign_changes").live('click',function(event){
	$.fancybox.close();
	window.open($('#preview_alert').attr('href'),'_blank');
});
/**
	cancel campaign preview
**/
$('#fancybox-wrap').find(".cancel_campaign_changes").live('click',function(event){
	$.fancybox.close();
});
/**
	Change alignment of image in image with  text block
**/
jQuery('.change-pos').live('click',function(){
	var image_align=jQuery(this).parents('.resize_table').attr('align');
	if(image_align=="left"){
		jQuery(this).find('img').attr('src','<?php echo base_url() ?>webappassets/images/align_left.png?v=6-20-13');
		jQuery(this).parents('.resize_table').attr('align','right');
		jQuery(this).parents('.resize_table td').attr('align','right');
		jQuery(this).parents('.resize_table').find('.active_image_option').css('padding-left','25px');
		jQuery(this).parents('.resize_table').css({'margin-left':'0px','margin-right':'0px'});
	}else{
		jQuery(this).find('img').attr('src','<?php echo base_url() ?>webappassets/images/align_right.png?v=6-20-13');
		jQuery(this).parents('.resize_table').attr('align','left');
		jQuery(this).parents('.resize_table td').attr('align','left');
		jQuery(this).parents('.resize_table').find('.active_image_option').css('padding-left','0px');
		jQuery(this).parents('.resize_table').css({'margin-left':'0px','margin-right':'0px'});
	}
	pagechange=true;		//set Page content change
});
/**
	Highlight border and display handler on mousover of logo
**/
jQuery("#logo").live('mouseover',function(){
	jQuery(this).find('.handler').show();
	jQuery(this).find('.logo-resize-div').show();
	jQuery(this).css('border-color','red');
}).live('mouseout',function(){
	jQuery(this).find('.handler').hide();
	jQuery(this).find('.logo-resize-div').hide();
	jQuery(this).css('border-color','transparent');
});
/**
	Open popup to edit Social media links
**/
$('.edit_social_media-link').live('click',function(){
	$.fancybox($("#social_media_dialog").html(),{'autoDimensions':true,'height':'482','width':'840','centerOnScroll':true,'onClosed':function() {closeSocialMedia();}});
	if($(this).parents('.container-div').find('.facebook_url_link').length>0){
		$('#fancybox-wrap').find('#facebox_url').val($(this).parents('.container-div').find('.facebook_url_link').attr('name'));
		$('#fancybox-wrap').find('#facebox_url').show();
		$('#fancybox-wrap').find('#add_facebook').hide();
		$('input[name=facebook_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.twitter_url_link').length>0){
		$('#fancybox-wrap').find('#twitter_url').val($(this).parents('.container-div').find('.twitter_url_link').attr('name'));
		$('#fancybox-wrap').find('#twitter_url').show();
		$('#fancybox-wrap').find('#add_twitter').hide();
		$('input[name=twitter_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.linkedin_url_link').length>0){
		$('#fancybox-wrap').find('#linkedin_url').val($(this).parents('.container-div').find('.linkedin_url_link').attr('name'));
		$('#fancybox-wrap').find('#linkedin_url').show();
		$('#fancybox-wrap').find('#add_linkedin').hide();
		$('input[name=linkedin_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.rss_url_link').length>0){
		$('#fancybox-wrap').find('#rss_url').val($(this).parents('.container-div').find('.rss_url_link').attr('name'));
		$('#fancybox-wrap').find('#rss_url').show();
		$('#fancybox-wrap').find('#add_rss').hide();
		$('input[name=rss_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.youtube_url_link').length>0){
		$('#fancybox-wrap').find('#youtube_url').val($(this).parents('.container-div').find('.youtube_url_link').attr('name'));
		$('#fancybox-wrap').find('#youtube_url').show();
		$('#fancybox-wrap').find('#add_youtube').hide();
		$('input[name=youtube_link]').attr('checked', true);
	}

	if($(this).parents('.container-div').find('.google_plus_url_link').length>0){
		$('#fancybox-wrap').find('#google_plus_url').val($(this).parents('.container-div').find('.google_plus_url_link').attr('name'));
		$('#fancybox-wrap').find('#google_plus_url').show();
		$('#fancybox-wrap').find('#add_google_plus').hide();
		$('input[name=google_plus_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.tumblr_url_link').length>0){
		$('#fancybox-wrap').find('#tumblr_url').val($(this).parents('.container-div').find('.tumblr_url_link').attr('name'));
		$('#fancybox-wrap').find('#tumblr_url').show();
		$('#fancybox-wrap').find('#add_tumblr').hide();
		$('input[name=tumblr_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.flickr_url_link').length>0){
		$('#fancybox-wrap').find('#flickr_url').val($(this).parents('.container-div').find('.flickr_url_link').attr('name'));
		$('#fancybox-wrap').find('#flickr_url').show();
		$('#fancybox-wrap').find('#add_flickr').hide();
		$('input[name=flickr_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.pinterest_url_link').length>0){
		$('#fancybox-wrap').find('#pinterest_url').val($(this).parents('.container-div').find('.pinterest_url_link').attr('name'));
		$('#fancybox-wrap').find('#pinterest_url').show();
		$('#fancybox-wrap').find('#add_pinterest').hide();
		$('input[name=pinterest_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.instagram_url_link').length>0){
		$('#fancybox-wrap').find('#instagram_url').val($(this).parents('.container-div').find('.instagram_url_link').attr('name'));
		$('#fancybox-wrap').find('#instagram_url').show();
		$('#fancybox-wrap').find('#add_instagram').hide();
		$('input[name=instagram_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.mailto_url_link').length>0){
		$('#fancybox-wrap').find('#mailto_url').val($(this).parents('.container-div').find('.mailto_url_link').attr('name'));
		$('#fancybox-wrap').find('#mailto_url').show();
		$('#fancybox-wrap').find('#add_mailto').hide();
		$('input[name=mailto_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.skype_url_link').length>0){
		$('#fancybox-wrap').find('#skype_url').val($(this).parents('.container-div').find('.skype_url_link').attr('name'));
		$('#fancybox-wrap').find('#skype_url').show();
		$('#fancybox-wrap').find('#add_skype').hide();
		$('input[name=skype_link]').attr('checked', true);
	}
	if($(this).parents('.container-div').find('.website_url_link').length>0){
		$('#fancybox-wrap').find('#website_url').val($(this).parents('.container-div').find('.website_url_link').attr('name'));
		$('#fancybox-wrap').find('#website_url').show();
		$('#fancybox-wrap').find('#add_website').hide();
		$('input[name=website_link]').attr('checked', true);
	}
	$("#current_container_id").val($(this).parents('.container-div').attr('id'));
});
/**
	Open popup to edit youtube media links
**/
$('.edit_youtube-link').live('click',function(){
	$.fancybox($("#youtube_edit_dialog").html(),{'autoDimensions':true,'height':'225','width':'475','centerOnScroll':true,'onClosed':function() {close_youtube();}});
	$('#fancybox-wrap').find("#youtube_url").val($(this).parents('.img_content').find('.image_link').attr('title'));
	$("#current_container_id").val($(this).parents('.container-div').attr('id')+"_edit");
});
/**
	Display blank on click of campaign_title input box
*/
jQuery("#campaign_title").live('click',function(){
	if(jQuery(this).val().toLowerCase()=="unnamed"){
		$("#current_container_id").val(jQuery(this).val());
		jQuery(this).val("");
	}
}).live('blur',function(){
	if(jQuery(this).val()==""){
		jQuery(this).val("Unnamed");
	}
	$("#current_container_id").val("");
});
/**
	Display Popup For feedback
**/
jQuery(".feedback_popup").live('click',function(){
	$.ajax({
        type        : "POST",
        cache       : false,
        url         : base_url+'feedback/create',
        data        : $(this).serializeArray(),
        success: function(data) {
			if(data){
				$.fancybox(data,{'autoDimensions':true,'height':'auto','width':'450','centerOnScroll':true});
			}
        }
    });
    return false;
});
$(document).ready(function(){
	/**
		Open Colorpicker for background color of email container
	**/
	$('#background_color_txt').ColorPicker({
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			//return false;
		},
		onHide: function (colpkr) {
			$(colpkr).hide(500);
			//return false;
		},
		onSubmit: function (hsb, hex, rgb,el) {
			$('#background_color_txt').val('#'+hex);
			$('#background_color_txt').css('color','#'+hex);
			changeStyle('body_main',hex);
			$(el).ColorPickerHide();
		}
	});
	/**
		Open Colorpicker for outer background color of email container
	**/
	$('#background_outer_txt').ColorPicker({
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onSubmit: function (hsb, hex, rgb,el) {
			$('#background_outer_txt').val('#'+hex);
			$('#background_outer_txt').css('color','#'+hex);
			changeStyle('main-table',hex);
			$(el).ColorPickerHide();
		}
	});
	/**
		Open Colorpicker for preheader font color  
	**/
	$('#preheader_color').ColorPicker({
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onSubmit: function (hsb, hex, rgb,el) {
			$('#preheader_color').val('#'+hex);
			$('#preheader_color').css('color','#'+hex);
			changeStyle('preheader',hex,'preheader');
			$(el).ColorPickerHide();
		}
	});
	/**
		Open Colorpicker for footer background color of email container
	**/
	$('#footer_txt').ColorPicker({
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onSubmit: function (hsb, hex, rgb,el) {
			$('#footer_txt').val('#'+hex);
			$('#footer_txt').css('color','#'+hex);
			changeStyle('footer',hex);
			$(el).ColorPickerHide();
		}
	});
	/**
		Open Colorpicker for border color of email container
	**/
	$('#border_txt').ColorPicker({
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onSubmit: function (hsb, hex, rgb, el) {
			$('#border_txt').val('#'+hex);
			$('#border_txt').css('color','#'+hex);
			changeStyle('email_template_table',hex,'border');
			$(el).ColorPickerHide();
		}
	});
	/**
		Open Colorpicker for footer color of email container
	**/
	$('#footer_color_txt').ColorPicker({
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onSubmit: function (hsb, hex, rgb,el) {
			$('#footer_color_txt').val('#'+hex);
			$('#footer_color_txt').css('color','#'+hex);
			$('#footer_color_txt').css('background-color','#'+hex);
			changeStyle('footer',hex,'font_color');
			$(el).ColorPickerHide();
		}
	});
	/**
		Open Popup  for edit  footer content
	**/
	jQuery("#footer_link_option").dialog({'autoOpen':false,'position':'center',
		'title':'Footer Options','modal':true,width:440,height: 465,
		open: function(event, ui) {
			jQuery.ajax({
				url: "<?php echo base_url() ?>account/get_user_info",
				type:"POST",
				success: function(data) {
					var data_arr=data.split('|');
					$('#company_name_footer').val(data_arr[0]);
					$('#address_footer').val(data_arr[1]);
					$('#city_footer').val(data_arr[2]);
					$('#state_footer').val(data_arr[3]);
					$('#zip_footer').val(data_arr[4]);
					$('#country_name_footer').val(data_arr[5]);
					$('.msg').html('');
				}
			});
		}
	});
	/**
		Display nice editor on click of text block
	**/
	var myNicEditor ;
	var isEditable = false;
	var div_id = 0;
	jQuery(".text-paragraph-container,.entry,.edit_offer, .preheader-text").live('mousedown',function(){
		if(isEditable == false){

			isEditable = true;
			div_id=	'div_'+jQuery(this).parents('.container-div').attr('id');
			jQuery(this).find('.empty_text').remove();
			jQuery(this).find('.empty_title').remove();
			jQuery(this).find('.empty_preheader').remove();
			jQuery(this).attr('id',div_id);
			// alert(jQuery(this).attr("class").substring(0,14));
			if('preheader-text' != jQuery(this).attr("class").substring(0,14)){			 
				myNicEditor = new nicEditor({buttonList : ['undo','redo','fontSize','fontFamily','bold','italic','underline','ol','ul','left','center','right','justify','link','unlink','forecolor','xhtml','insertHTML']}).setPanel('myNicPanel').addInstance(div_id);
			}else{			
				myNicEditor = new nicEditor({buttonList : null}).setPanel('myNicPanel').addInstance(div_id);
			}
			jQuery(this).addClass('text-highlighted');
			jQuery('#'+div_id).focus();
			jQuery(this).attr("tabindex","-1");
			setTimeout('jQuery("#'+div_id+'").focus();',300);
			pagechange=true;	//Page content change

			// Stick Top Header
			var testTopScroll = function() {
				var scroll = $(window).scrollTop();
				if(scroll < 40) {
					$("#myNicPanel .nicEdit-panelContain").css("marginTop",40 - scroll);
				} else {
					$("#myNicPanel .nicEdit-panelContain").css("marginTop",0);
				}
			};
			testTopScroll();
			$(window).scroll(testTopScroll);
		}
	});
	/**
		Hide nice editor on click of html body
	**/
	$("html").click(function (evt) {
		var target = evt.target;
		if (!($(target).closest('#'+div_id).length)  && !($(target).closest('.nicEdit-pane').length) && !($(target).closest('.nicEdit-panel').length)  &&  (isEditable)){
			isEditable = false;
			$('#myNicPanel').html('');
			myNicEditor.removeInstance(div_id);
			myNicEditor = null;
			if(jQuery('#'+div_id).hasClass('header-text')){
				if((jQuery('#'+div_id).html()=="")||(jQuery('#'+div_id).html()=="<br>")||(jQuery('#'+div_id).html()=="<BR>")){
					jQuery('#'+div_id).html("<div class='empty_title'>This is a title block. Click here to add a title.</div>");
				}
			}else if(jQuery('#'+div_id).hasClass('text-paragraph-container')){
				if((jQuery('#'+div_id).html()=="")||(jQuery('#'+div_id).html()=="<BR>")||(jQuery('#'+div_id).html()=="<br>")){
					jQuery('#'+div_id).html("<div class='empty_text'>This is a text block. Click here to add text.</div>");
				}
			}else if(jQuery('#'+div_id).hasClass('preheader-text')){
				if((jQuery('#'+div_id).html()=="")||(jQuery('#'+div_id).html()=="<BR>")||(jQuery('#'+div_id).html()=="<br>")){
					jQuery('#'+div_id).html("<div class='empty_preheader'>This is a pre-header. Here you can write a short preview of your email content.</div>");
				}
			}
			jQuery('.resize-div').css('z-index','10');
			jQuery('#'+div_id).removeClass('text-highlighted');
			jQuery('#'+div_id).removeAttr('contenteditable');
		}
	});

	/**
		Block Sorting Function
	**/
	for(var i=0;i<body_blocks.length;i++){
		jQuery("#"+body_blocks[i]).sortable({items:'.container-div,.move_img_container',
		handle:'.drag_handler,.handler_move',
		cursor:'move',
		connectWith:'#body_main',
		receive:function(event,ui){
			if(jQuery(this).find('.empty-text').length>0){
				jQuery(this).find('.empty-text').remove();
				jQuery(this).css("background-image","none");
				jQuery(this).css("min-height","0px");
			}
		},
		remove:function(event,ui){
			if(jQuery(this).find('.container-div').length<1)
			{
				jQuery(this).empty().append('<p class="empty-text"></p>');
			}
		}
		});
	}
	/**
		Add tool tip
	**/
	drag_drop();	// Load drag_drop on load of page
	loadHeaderEffect();	// Load header effects on load of page
	loadFooterEffect();	//Load footer effects on load of page
	imageResize();		// Load image resizer on load of page
	loadLogoEffect();		// Load logo effects  on load of page
	loadOfferEffects();		// Load Offer block effects on load of page
	loadImageEffects();		// Load image effects on load of page
	customColors();			// Load custom colors effects on load of page
});

/**
	Confirmation message before leaving page
**/
window.onbeforeunload = askConfirm;

function askConfirm(){
    if (pagechange){
        return  "Your Campaign has unsaved changes. Any unsaved changes will be lost!\n" +
           "Would you still like to exit without saving??";
    }
}
function showDIYDemo(){
	$.fancybox('<iframe width="640" height="365" src="https://www.youtube.com/embed/I33CzirhlxA?rel=0" frameborder="0" allowfullscreen></iframe>');
}
$(document).ready(function(){
	/* Append video link in default/blank template */
	//if($('.container-div').length <=1){
	if($('#body_main').children('.container-div').length < 1){
		pagechange=true;
		$('.diy_demo_video').remove();
		$('#body_main').append(el_diy_demo_video);
	}

  var followScroll = function() {
    if($(window).height() > $("div.tabs-editor").height()) {
      var $leftMenu = $("div.tabs-editor"),
          top = $leftMenu.offset().top - parseFloat($leftMenu.css('marginTop').replace(/auto/,0));

      $(window).scroll(function() {
        var y = $(this).scrollTop();

        if (y >= top) {
          $leftMenu.addClass('fixed');
        } else {
          $leftMenu.removeClass('fixed');
        }
      });
    } else {
      $(window).unbind("scroll");
      $("div.tabs-editor").removeClass("fixed").removeAttr("style");
    }
  };

  followScroll();

  $(window).resize(function() {
    followScroll();
  });
});

