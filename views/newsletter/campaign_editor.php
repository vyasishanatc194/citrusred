<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=1153"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="x-ua-compatible" content="IE=8; IE=9" />
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
<title>Easy to use drag-and-drop Email Builder, Email Marketing Software, Email Marketing Tool</title>
<!--------------Load Css------------------------------->
<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">
<script type="text/javascript">
	/********Global Variables***************/
	var ie=0;
	var base_url="<?php echo base_url();?>";
	var margin_top=0;
	var email_campaign_id=<?php echo $email_template_info['campaign_id']; ?>;
	var campaign_color_theme_id=<?php echo $email_template_info['campaign_color_theme_id']; ?>;
	var user_id=<?php echo $member_id; ?>;
	var logo_dialog_box=0;
	var header_image_change=1;
	var max_font_size=40;
	var min_font_size=10;
	var preview_page=false;
	var size="<?php echo $footer_font_txt; ?>";
	if(size){
		var footer_font_size=parseInt(size);
	}else{
		var footer_font_size=10;
	}
	<?php if($is_auotresponder){ ?>
		var campaign_text="auotresponder";
	<?php }else{ ?>
		var campaign_text="campaign";
	<?php } ?>

</script>
<!--[if IE]>
<script type="text/javascript">ie = 1;</script>
<![endif]-->
<script type="text/javascript" src="<?php echo base_url(); ?>newsletter/campaign/campaign_editor_js/"></script>
<script type="text/javascript">
//<![CDATA[
<?php #include_once("campaign_editor.js.php");?>
 //]]>
</script>
<link href="<?php echo $this->config->item('webappassets');?>css/diy.css?v=6-20-13" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url(); ?>newsletter/campaign/campaign_editor_style/">
<style type="text/css" media="all" >
<?php #include_once("campaign_editor_style.php");?>
#template a, #template a:visited {
	color: #4285C6;
	text-decoration:underline;
}
h3{font-size:17px;font-weight:bold;margin:0 10px 0 10px;}
</style>
<link href="<?php echo $this->config->item('webappassets');?>css/colorpicker.css?v=6-20-13" rel="stylesheet" type="text/css" />
<?php $ci =& get_instance(); //var_dump($website_info); ?>
<?php if($is_auotresponder){ ?>
<?php echo form_open('newsletter/autoresponder/change_template', array('id' => 'form_change_template','name' => 'form_change_template')); ?>
<?php }else{ ?>
<?php echo form_open('newsletter/campaign/change_template', array('id' => 'form_change_template','name' => 'form_change_template')); ?>
<?php } ?>
<input type="hidden" id="preview_template_id" name="preview_template_id" value="<?php echo $template_data[0]['template_id']; ?>">
<input type="hidden" id="current_tab_page" value="<?php echo $pages[0]['id']; ?>">
<input type="hidden" id="is_page_changed" name="is_page_changed" value="0" />
<?php if($email_template_info['campaign_email_content']==""){?>
	<input type="hidden" id="current_template_id" value="<?php echo $template_info['template_id']; ?>" />
<?php }else{ ?>
	<input type="hidden" id="current_template_id" value="-1" />
<?php } ?>
<?php echo form_close(); ?>
</head>
<body class="outer_bg" >
<div id="demo-tips">

<div id="wrapper">
	<div class="feedback"><a  class="feedback_popup"><img src="<?php echo $this->config->item('webappassets');?>images/feedback.png?v=6-20-13" /></a></div>
	<div id="diy-header">
		<div id="header-logo">
			<?php if($is_auotresponder){ ?>
				<a href="<?php echo  site_url("newsletter/autoresponder");?>">
			<?php }else{ ?>
				<a href="<?php echo  site_url("newsletter/campaign");?>">
			<?php } ?>
				<img src="<?php echo $this->config->item('webappassets');?>images/redcappi-baby-editor.png?v=6-20-13" alt="" border="0"/>
			</a>
		</div>
		<form action="" method="post">
			<label class="label">Campaign Name</label>
			<input type="text"  id="campaign_title" name="campaign_title" value="<?php echo $email_template_info['campaign_title']; ?>" onchange="javascript:pagechange=true;" />
			<a class="save_campaign_changes btn cancel" title="Save" style="margin: 2px 5px 0 10px"><i class="icon-save"></i></a>
			<?php if($is_auotresponder){ ?>
				<a <?php if($email_template_info['campaign_template_option']==3){ ?> class="btn add save_campaign_changes" <?php } ?>  href="<?php echo  site_url('newsletter/campaign_email_setting/autoresponder/'.$email_template_info['campaign_id']);?>" id="next_step" onclick="return false;">Next Step</a>
			<?php }else{ ?>
				<a <?php if($email_template_info['campaign_template_option']==3){ ?> class="btn add save_campaign_changes" <?php } ?>  href="<?php echo  site_url('newsletter/campaign_email_setting/index/'.$email_template_info['campaign_id']);?>" id="next_step" onclick="return false;">Next Step</a>
			<?php } ?>
    </form>
		<?php if($is_auotresponder){ ?>
			<a href="<?php echo  site_url("newsletter/autoresponder");?>" title="Close" class="btn cancel inline-block remove-icon"><i class="icon-remove"></i></a>
		<?php }else{ ?>
			<a href="<?php echo  site_url("newsletter/campaign");?>" title="Close" class="btn cancel inline-block remove-icon"><i class="icon-remove"></i></a>
		<?php } ?>
		<a href="<?php echo  site_url("support/index");?>" target="_blank" title="Help" class="btn cancel inline-block"><i class="icon-question"></i></a>
		<?php if($is_auotresponder){ ?>
				<a class="btn cancel inline-block" title="Preview" href="<?php echo CAMPAIGN_DOMAIN.'a/'.$email_template_info['campaign_id']; ?>" id="preview_alert"><i class="icon-eye-open"></i></a>
		<?php }else{ ?>
				<a class="btn cancel inline-block" title="Preview" href="<?php echo CAMPAIGN_DOMAIN.'c/'.$email_template_info['campaign_id'] ;?>" id="preview_alert"><i class="icon-eye-open"></i></a>
		<?php } ?>
	</div>
  <div class="main-container">
    <table width="100%" border="0" cellpadding="0" align="c" cellspacing="0"  id="main-table" name="main-table">
      <tr>
        <td width="242"  align="left" valign="top">
		<div class="left-side">
          <div class="tabs-editor">
            <div class="item-container" >
              <div class="items">
              	<a class="toolbox tab selected" title="Tool Box"  onclick="toolboxDisplay();"><img src="" alt="" /><img src="<?php echo base_url() ?>webappassets/images/content-title.png?v=6-20-13" alt="Theme Title" />Content<i class="icon-angle-down"></i><i class="icon-angle-up"></i></a>
                <div id="one">
                  <ul class="toolbar">
                    <li><div class="block-text text-paragraph black"><img src="<?php echo $this->config->item('webappassets');?>images/text.png?v=6-20-13" /><span class="colorTip">Drag to Email<span class="pointyTipShadow"></span><span class="pointyTip"></span></span></div></li>
										<li><div class="block-title black"><img src="<?php echo $this->config->item('webappassets');?>images/title.png?v=6-20-13" /><span class="colorTip">Drag to Email<span class="pointyTipShadow"></span><span class="pointyTip"></span></span></div></li>
										<li><div class="block-image black"><img src="<?php echo $this->config->item('webappassets');?>images/image.png?v=6-20-13" /><span class="colorTip">Drag to Email<span class="pointyTipShadow"></span><span class="pointyTip"></span></span></div></li>
                    <li><div class="block-image-text black"><img src="<?php echo $this->config->item('webappassets');?>images/text-with-image.png?v=6-20-13" /><span class="colorTip">Drag to Email<span class="pointyTipShadow"></span><span class="pointyTip"></span></span></div></li>
                    <li><div class="block-offer black"><img src="<?php echo $this->config->item('webappassets');?>images/coupon.png?v=6-20-13" /><span class="colorTip">Drag to Email<span class="pointyTipShadow"></span><span class="pointyTip"></span></span></div></li>
										<li><div class="block-divider-rule black"><img src="<?php echo $this->config->item('webappassets');?>images/divider.png?v=6-20-13" /><span class="colorTip">Drag to Email<span class="pointyTipShadow"></span><span class="pointyTip"></span></span></div></li>
										<li><div class="block-youtube black"><img src="<?php echo $this->config->item('webappassets');?>images/video_block.png?v=6-20-13"><span class="colorTip">Drag to Email<span class="pointyTipShadow"></span><span class="pointyTip"></span></span></div></li>
										<li><div class="block-social-media black"><img src="<?php echo $this->config->item('webappassets');?>images/social-media.png?v=6-20-13" /><span class="colorTip">Drag to Email<span class="pointyTipShadow"></span><span class="pointyTip"></span></span></div></li>
                  </ul>
                </div>
                <a class="imagebank tab" title="Image Bank"  onclick="imageBankDisplay();"><img src="<?php echo base_url() ?>webappassets/images/image-title.png?v=6-20-13" alt="Theme Title" />Images<i class="icon-angle-down"></i><i class="icon-angle-up"></i></a>
				<div id="two" >
					<div class="div_get_images">
						<a class="btn cancel upload_image_bank"><i class="icon-plus"></i>Upload Images</a>
					</div>
					<div  class="img_bank_div">
						<ul class="img-bank">
							<li class="load_images">
								<img src="<?php echo base_url() ?>webappassets/images/ajax-loader.gif?v=6-20-13" border="0"/>
							</li>
						</ul>
					</div>
	      </div>
        <a class="color tab" title="Color"  onclick="colorboxDisplay();"><img src="<?php echo base_url() ?>webappassets/images/theme-title.png?v=6-20-13" alt="Theme Title" />Themes<i class="icon-angle-down"></i><i class="icon-angle-up"></i></a>
				<div id="three" style="display:none; ">
					<ul class="colorstab">
						<li class="default active touch-left btn cancel"  onclick="javascript:$('#default-theme').slideDown();$('#custom-theme').slideUp(); $('.colorstab li').removeClass('active'); $(this).addClass('active');"><a >Themes</a> </li>
						<li class="custom touch-right btn cancel" onclick="javascript:$('#custom-theme').slideDown();$('#default-theme').slideUp(); $('.colorstab li').removeClass('active');$(this).addClass('active');"><a >Custom</a></li>
					</ul>
					<div class="theme-selection"  id="custom-theme"  style="display:none;clear:both;">
						<div  id="div2" style="display:block; overflow:auto; height:319px;"  class="theme-div-2">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tbl-normal custom_color_option" >
								<tr>
									<td colspan="2">
										<div class="theme_color_info" style="padding: 3px 10px; margin: 0"></div>
									</td>
								</tr>
								<tr>
									<td>Background</td>
									<td align="right"><input id="background_color_txt"  class="color background-color body_bg_color" value="<?php echo $body_main; ?>" style="width:60px;height:15px;background-color:#<?php echo $body_main; ?>;color:#<?php echo $body_main; ?>" ></td>
								</tr>
								<tr>
									<td>Outer Background</td>
									<td align="right"><input id="background_outer_txt" class="color background-color outer_bg" value="<?php echo $outer_background; ?>" style="width:60px;height:15px;background-color:#<?php echo $outer_background; ?>;color:#<?php echo $outer_background; ?>;">
									</td>
								</tr>
								<tr>
									<td>Pre-header Font-color</td>
									<td align="right"><input id="preheader_color" class="color background-color preheader_color" value="<?php echo $preheader_font_color; ?>" style="width:60px;height:15px;background-color:#<?php echo $preheader_font_color; ?>;color:#<?php echo $preheader_font_color; ?>;">
									</td>
								</tr>
								<tr>
									<td>Footer Background</td>
									<td align="right"><input id="footer_txt" class="color background-color footer_txt_color" value="<?php echo $footer; ?>" style="width:60px;height:15px;background-color:#<?php echo $footer; ?>;color:#<?php echo $footer; ?>;" ></td>
								</tr>
								<tr>
									<td>Border</td>
									<td align="right"><input id="border_txt" class="color border-color body_border" value="<?php echo $body_border; ?>" style="width:60px;height:15px;background-color:#<?php echo $body_border; ?>;color:#<?php echo $body_border; ?>" ></td>
								</tr>
								<tr>
									<td>Border Style</td>
									<td align="right">
										<select id="body-options-border" class="border border-style" onchange="changeStyle('email_template_table','','border_style');">
											<option value="thin">Thin</option>
											<option value="solid">Normal</option>
											<option value="thick">Thick</option>
											<option value="dashed">Dashed</option>
											<option value="none">None</option>
										</select>
									</td>
								</tr>
									<tr>
									<td colspan="2" align="center">
										<div>
											<a style="text-decoration:none;padding:7px 11px 6px;font-size:16px;width:110px;margin:20px 0 0 45px;" class="btn add fl add_color_theme color_theme_dialog" onclick="javascript:$('.color_theme_dialog').toggle();" title="Add New Theme" >
												<i class="icon-plus" style="margin-right: 5px; font-size: 12px;position: relative;bottom: 1px;"></i>Add Theme
											</a>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2" height="5"></td>
								</tr>
								<tr style="display:none;" class="color_theme_dialog">
									<td colspan="2">
										<strong style="font-size: 15px;display: block;margin-bottom: 2px; -webkit-font-smoothing: antialiased">Theme Name</strong>
									</td>
								</tr>
								<tr  style="display:none;" class="color_theme_dialog">
									<td colspan="2" style="padding: 0">
										<input type="text" name="color_theme_name" id="color_theme_name" style="width: 200px;margin: 2px 0;padding: 4px 8px;height: 23px" class="input-radius"  />
									</td>
								</tr>
								<tr style="display:none;" class="color_theme_dialog">
									<td>
										<a onclick="saveThemeColor();" style="padding: 4px 17px 4px;font-size: 15px; text-decoration: none; margin: 4px 0 0" class="btn add fl" >
											Save
										</a>
									</td>
									<td>
										<a style="padding: 4px 17px 4px;font-size: 15px; text-decoration: none; margin: 4px 0 0" onclick="javascript:$('.color_theme_dialog').toggle();" title="Add New Theme"  class="btn cancel fl" >
											Cancel
										</a>
									</td>
								</tr>
							</table>
							<table cellpadding="4" cellspacing="4" class="tbl-normal" style="display:none;">
								<tr>
									<td colspan="2">
										<div class="theme_color_info"></div>
									</td>
								</tr>
								<tr>
									<td>
										Theme Name
									</td>
									<td>
										<input type="text" name="color_theme_name" id="color_theme_name" size="5"  />
									</td>
								</tr>
								<tr>
									<td>
										Header Color
									</td>
									<td>
										<input type="text" id="theme_header_color" class="color" size="1" style="width:15px;height:15px;" readonly />
									</td>
								</tr>
								<tr>
									<td>
										Body  Color
									</td>
									<td>
										<input type="text" id="theme_body_color" class="color" size="1" style="width:15px;height:15px;" readonly />
									</td>
								</tr>
								<tr>
									<td>
										Footer  Color
									</td>
									<td>
										<input type="text" id="theme_footer_color" class="color" size="1" style="width:15px;height:15px;" readonly />
									</td>
								</tr>
								<tr>
									<td>
										Border Color
									</td>
									<td>
										<input type="text" id="theme_border_color" class="color" size="1" style="width:15px;height:15px;" readonly />
									</td>
								</tr>
								<tr>
									<td>
										Body Font Color
									</td>
									<td>
										<input type="text" id="theme_body_font_color" class="color" size="1" style="width:15px;height:15px;" readonly />
									</td>
								</tr>
								<tr>
									<td>
										Footer Font Color
									</td>
									<td>
										<input type="text" id="theme_footer_font_color" class="color" size="1" style="width:15px;height:15px;" readonly />
									</td>
								</tr>
								<tr>
									<td>
										Outer Color
									</td>
									<td>
										<input type="text" id="theme_outer_bg_color" class="color" size="1" style="width:15px;height:15px;" readonly />
									</td>
								</tr>

								<tr>
									<td><a  onclick="save_theme_color();" style="text-decoration:none;" class="button-red fl" ><span>Save</span></a></td>
									<td>
										<a  style="text-decoration:none;" onclick="javascript:$('.color_theme_dialog').toggle();$('.custom_color_option').toggle();" title="Add New Theme"  class="button-red fl" ><span>Cancel</span></a>

									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="theme-selection"  id="default-theme">
						<div  id="div1" style="display:block;"  >
							<div style="display:block; height:339px;overflow:auto"  >
								<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="table_color" id="default_theme_color">
									<tr class="load_colors">
										<td style="text-align:center;">
											<img src="<?php echo base_url() ?>webappassets/images/ajax-loader.gif?v=6-20-13" border="0"/>
										</td>
									</tr>
								</table>
							</div>
					  </div>
					</div>
					</div>
              </div>
            </div>
          </div>
          </div>
		  </td>

		  <td>&nbsp;</td>
        <td align="left" valign="top" class="right-side">
          <div class="diy-editor">
            <div id="template_container" class="editor-toolbar">
				<?php if($email_template_info['campaign_email_content']!=""){?>
				<?php echo htmlspecialchars_decode (html_entity_decode($email_template_info['campaign_email_content'], ENT_QUOTES, "utf-8" )); ?>
				<?php }else{ ?>
				<?php echo $theme_css; ?>
				<?php echo ($template_info['filtered_html']); ?>
				<?php } ?>
            </div>
          </div></td>
      </tr>
    </table>
	<!-- Image group popup box -->
	<div id="image_group_dialog" style="display:none;">
			<form action="#" method="post" id="select-images">
				<h5>Add Image</h5>
				<p style="margin: 10px 23px 0">
					<strong>Select the number of images to insert in this block:</strong>
				</p>
					<ol>
						<li><a onclick="saveImageGroupOption('1')"><img src="<?php echo base_url() ?>webappassets/images/select-img-1.png?v=6-20-13" alt="" /></a></li>
						<li><a onclick="saveImageGroupOption('2')"><img src="<?php echo base_url() ?>webappassets/images/select-img-2.png?v=6-20-13" alt="" /></a></li>
						<li><a onclick="saveImageGroupOption('3')"><img src="<?php echo base_url() ?>webappassets/images/select-img-3.png?v=6-20-13" alt="" /></a></li>
						<li><a onclick="saveImageGroupOption('4')"><img src="<?php echo base_url() ?>webappassets/images/select-img-4.png?v=6-20-13" alt="" /></a></li>
					</ol>
			</form>
		<div class="clear"></div>
	</div>

	<!-- social media popup box -->
	<div id="social_media_dialog" style="display:none;">
		<h5>Social</h5>
		<p style="margin: 10px 15px 15px">
			<strong>Select the icons and type the link to your page.</strong>
		</p>
		<table style="border-collase: collapse; width:800px;" border="0" cellpadding="0">
		<tr>
		<td width="50%">
		<table style="border-collase: collapse; margin: 7px 15px; height: 306px; width:380px;" cellpadding="4">
		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/facebook-share.png?v=6-20-13" style="width:36px; height:36px;" title="facebook" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="facebook_link" id="facebook_link" />
			</td>
			<td class="facebook_text" width="75%">
				<input type="text" name="facebox_url" id="facebox_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_facebook">Insert Facebook Icon</span>
			</td>
		</tr>
		<tr>
			<td>
				<img src="<?php echo $this->config->item('webappassets');?>images/twitter-share.png?v=6-20-13" style="width:36px; height:36px;" title="twitter" />
			</td>
			<td align="center">
				<input type="checkbox" value="1" name="twitter_link" id ="twitter_link" />
			</td>
			<td class="twitter_text">
				<input type="text" name="twitter_url" id="twitter_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_twitter">Insert Twitter Icon</span>
			</td>
		</tr>
		<tr>
			<td>
				<img src="<?php echo $this->config->item('webappassets');?>images/linkedin-share.png?v=6-20-13" style="width:36px; height:36px;" title="Linkedin" />
			</td>
			<td align="center">
				<input type="checkbox" value="1" name="linkedin_link" id ="linkedin_link" />
			</td>
			<td class="linkedin_text">
				<input type="text" name="linkedin_url" id="linkedin_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_linkedin">Insert LinkedIn Icon</span>
			</td>
		</tr>
		<tr>
			<td>
				<img src="<?php echo $this->config->item('webappassets');?>images/rss_icon.png?v=6-20-13" style="width:36px; height:36px;" title="Blog/Rss" />
			</td>
			<td align="center">
				<input type="checkbox" value="1" name="rss_link" id ="rss_link" />
			</td>
			<td class="rss_text">
				<input type="text" name="rss_url" id="rss_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_rss">Insert RSS Icon</span>
			</td>
		</tr>
		<tr>
			<td>
				<img src="<?php echo $this->config->item('webappassets');?>images/youtube.png?v=6-20-13" style="width:36px; height:36px;" title="Blog/Rss" />
			</td>
			<td align="center">
				<input type="checkbox" value="1" name="youtube_link" id ="youtube_link" />
			</td>
			<td class="youtube_text">
				<input type="text" name="youtube_url" id="youtube_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_youtube">Insert YouTube Icon</span>
			</td>
		</tr>
		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/google-plus-share.png?v=6-20-13" style="width:36px; height:36px;" title="Google+" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="google_plus_link" id="google_plus_link" />
			</td>
			<td class="google_plus_text" width="75%">
				<input type="text" name="google_plus_url" id="google_plus_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_google_plus">Insert Google+ Icon</span>
			</td>
		</tr>
		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/skype-share.png?v=6-20-13" style="width:36px; height:36px;" title="Skype" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="skype_link" id="skype_link" />
			</td>
			<td class="skype_text" width="75%">
				<input type="text" name="skype_url" id="skype_url" style="display:none;width:220px" placeholder="Enter the skype-id here..."/>
				<span id="add_skype">Insert Skype Icon</span>
			</td>
		</tr>
		</table></td>
		<td>
		<table style="border-collase: collapse; margin: 7px 15px; height: 306px;width:380px;" cellpadding="4">

		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/tumblr-share.png?v=6-20-13" style="width:36px; height:36px;" title="tumblr" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="tumblr_link" id="tumblr_link" />
			</td>
			<td class="tumblr_text" width="75%">
				<input type="text" name="tumblr_url" id="tumblr_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_tumblr">Insert Tumblr Icon</span>
			</td>
		</tr>
		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/flickr-share.png?v=6-20-13" style="width:36px; height:36px;" title="Flickr" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="flickr_link" id="flickr_link" />
			</td>
			<td class="flickr_text" width="75%">
				<input type="text" name="flickr_url" id="flickr_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_flickr">Insert Flickr Icon</span>
			</td>
		</tr>
		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/pinterest.png?v=6-20-13" style="width:32px; height:32px;" title="Pinterest" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="pinterest_link" id="pinterest_link" />
			</td>
			<td class="pinterest_text" width="75%">
				<input type="text" name="pinterest_url" id="pinterest_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_pinterest">Insert Pinterest Icon</span>
			</td>
		</tr>
		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/instagram.png?v=6-20-13" style="width:32px; height:32px;" title="Instagram" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="instagram_link" id="instagram_link" />
			</td>
			<td class="instagram_text" width="75%">
				<input type="text" name="instagram_url" id="instagram_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_instagram">Insert Instagram Icon</span>
			</td>
		</tr>
		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/email-share.png?v=6-20-13" style="width:32px; height:32px;" title="Mailto" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="mailto_link" id="mailto_link" />
			</td>
			<td class="mailto_text" width="75%">
				<input type="text" name="mailto_url" id="mailto_url" style="display:none;width:220px" placeholder="Enter the email-id here..."/>
				<span id="add_mailto">Insert Email Address Icon</span>
			</td>
		</tr>
		<tr>
			<td width="10%">
				<img src="<?php echo $this->config->item('webappassets');?>images/website-share.png?v=6-20-13" style="width:32px; height:32px;" title="Website" />
			</td>
			<td width="15%" align="center">
				<input type="checkbox" value="1" name="website_link" id="website_link" />
			</td>
			<td class="website_text" width="75%">
				<input type="text" name="website_url" id="website_url" style="display:none;width:220px" placeholder="Enter the URL here..."/>
				<span id="add_website">Insert Website Icon</span>
			</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		<div class="btn-group">
			<span class="image_group_span"><a onclick="socialMediaUrlSubmit();" class="btn add">Submit</a></span>
		</div>
		<input type="hidden" id="current_container_id" name="current_container_id">
	</div>
	<!-- Youtube block popup box -->
	<div id="youtube_edit_dialog" style="display:none;">
		<h5>Add Video</h5>
		<p style="padding: 0 15px">
			<strong>Simply paste the URL of the video you want to embed. We accept embeds from Vimeo and YouTube.</strong>
		</p>
		<span colspan="2" class="youtube_msg" style="color:red;font-weight:bold;"></span>
		<p>
			<input type="text" name="youtube_url" class="img_link" id="youtube_url" style="width: 400px" placeholder="Enter the URL here..." />
		</p>
		<div class="btn-group">
			<span class="image_group_span"><a onclick="checkYoutubevideoOrVimeovideo();" class="btn add">Submit</a></span>
		</div>
	</div>

	<!-- Confirm message popup box -->
	<div id="confirm_msg" style="display:none;">
		<div class="confirm_msg_div">
			<h5>Confirm</h5>
			<p>Are you sure you want to delete this block?</p>
			<input type="hidden" name="element_name" id="element_name" />
			<div class="btn-group">
				<button class="btn add delete-block">Yes</button>
				<button class="btn cancel cancel_delete-link">No</button>
			</div>
		</div>
	</div>
	<!-- Confirm message popup box for Image removal-->
	<div id="confirm_msg_img_remove" style="display:none;">
		<div class="confirm_msg_div">
			<h5>Confirm</h5>
			<p>Are you sure you want to delete this image?</p>
			<input type="hidden" name="element_name" id="element_name" />
			<div class="btn-group">
				<button class="btn add delete-block">Yes</button>
				<button class="btn cancel cancel_delete-link">No</button>
			</div>
		</div>
	</div>
	<!-- Image caption popup box -->
	<div id="image_caption" style="display:none;">
		<h5>Add a caption</h5>
		<div style="clear:both"></div>
		<textarea name="image_link_caption" id="image_link_caption" placeholder="Enter caption..."></textarea>
		<div style="clear:both;height:10px;"></div>
		<div class="btn-group">
			<a onclick="saveImageCaption();" class="btn add image_option_submit">Submit</a>
		</div>
	</div>
	<!-- Image Link popup box -->
	<div id="image_option" style="display:none;">
		<h5>Add link</h5>
		<div style="clear:both"></div>
		<input name="image_link" id="image_link" class="image_link" type="text" placeholder="Enter the URL here..." />
		<div class="clear_image_link"></div>
		<div class="btn-group">
			<a onclick="saveImageLink();" class="btn add image_option_submit" >Submit</a>
		</div>
	</div>
	<!-- Header Link popup box -->
	<div id="header_link_option">
		<h5>Add link</h5>
		<input name="header_link_text" id="header_link_text" class="image_link" type="text" />
		<a onclick="saveHeaderLink();" class="btn add header_link_submit" style="margin-right:15px !important">Submit</a>
	</div>
	<!-- Logo  popup box -->
	<div id="logo_dialog" style="display:none;">
		<div class="logo_dialog">
			<form action="#" method="post">
			<h5>Upload Logo</h5>
			<input name="logo_file" id="logo_file" type="file">
			</form>
		</div>
	</div>
	<!-- Upload image in image bank popup box -->
	<div id="upload_image_bank_dialog" style="display:none;height:160px; margin:5px 0px;">
			<h5>Upload Image</h5>
			<div id="image_bank_file_container">
				<p class="img_upload_msg"></p>
				<input name="image_bank_file" id="image_bank_file" type="file" />
				<!--div style="clear:both;height:10px;"></div>
				<span class="uplaod_image_url">
				OR
				<p>
				Upload Image using URL
				<div style="clear:both"></div>
					<div style="flotat:left"><input name="image_bank_url" id="image_bank_url" type="text" /></div><div style="float:left;"> <a  onclick="save_image_bank_url();" class="button-red fl" style="margin:10px 0 0px 10px;"><span>Upload</span></a></div>
				</p>
				</span-->
			</div>
		</div>
	</div>
	<div id="block_upload_image_bank_dialog" style="display:none;height:160px; margin:5px 0px;">
		<div style="float:left;margin:0px 20px;width:300px;">
			<p class="img_upload_msg"></p>
			<h5>Quota Exceeded</h5><br/>
			<div style="clear:both"></div>
			<p>
				Size of your image-bank has exceeded the allowed limit. To upload image, you need to remove some of your already uploaded image from your image-library.
			</p>
		</div>
	</div>
	<div id="footer_link_option" style="display:none;">
		<table width="95%" align="center" style="margin-top: 8px">
			<tr>
				<td colspan="2" class="msg" style="font-color:red;"></td>
			</tr>
			<tr>
				<td class="td_footer">Company Name</td>
				<td><input type="text" name="company_name_footer" id="company_name_footer" value="<?php echo $user_data['company'];?>" size="40"/></td>
			</tr>
			<tr>
				<td class="td_footer">Address</td>
				<td><input type="text" name="address_footer" id="address_footer" value=" <?php echo $user_data['address_line_1'];?>" size="40" /></td>
			</tr>
			<tr>
				<td class="td_footer">City</td>
				<td><input type="text" name="city_footer" id="city_footer"  value=" <?php echo $user_data['city'];?>" size="40" /></td>
			</tr>
			<tr>
				<td class="td_footer">State or Province</td>
				<td><input type="text" name="state_footer" id="state_footer"  value=" <?php echo $user_data['state'];?>" size="40" /></td>
			</tr>
			<tr>
				<td class="td_footer">Zip/Postal Code</td>
				<td><input type="text" name="zip_footer" id="zip_footer"  value=" <?php echo $user_data['zipcode'];?>" size="40" /></td>
			</tr>
			<tr>
				<td class="td_footer">Country</td>
				<td>
					<select name="country_name_footer" id="country_name_footer" style="height:34px;width:241px" class="country_footer" onchange="javascript: showCustom(this);">
							<?php
								if($user_data['country_id']){
									$selectd_id=$user_data['country_id'];
								}else{
									$selectd_id=225;
								}
								foreach($country_info as $country){
									if($country['country_id']==$selectd_id){
										echo "<option value='".$country['country_id']."' selected>".$country['country_name']."</option>";
									}else{
										echo "<option value='".$country['country_id']."'>".$country['country_name']."</option>";
									}
								}
							?>
					</select>
					<div style="height:29px;"><span id="country_custom_div"><input type="text" maxlength="50" name="country_custom_name_footer" id="country_custom_name_footer" value="<?php echo  $user_data['country_custom'];?>" /></span></div>
				</td>
			</tr>
			<tr>
				<td class="td_footer">Font Color</td>
				<td>
				<input id="footer_color_txt" class="color font-color" value="<?php echo $footer_color_txt; ?>" style="width:60px;height:15px;color:transparent;background-color:#<?php echo $footer_color_txt; ?>;"  />
				</td>
			</tr>
			<tr>
				<td class="td_footer">Font Size</td>
				<td>
					<select onchange="changeStyle('footer','footer_font_size','font_size');" class="select_font_size" id="footer_font_size">
						<option value="9px" size="1" >1</option>
						<option value="11px" size="2" >2</option>
						<option value="13px" size="3" >3</option>
						<option value="15px" size="4" >4</option>
						<option value="17px" size="5">5</option>
					</select>
					<span class="selected_font" style="font-size:17px;">Abc</span>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"><a onclick="saveFooterText();" class="btn add">Submit</a></td>
				</td>
			</tr>
		</table>

	</div>
	<div style="display:none;" id="preview_msg" class="">
		<h2 style="font-weight: 300;background-color: #f8f8f8;padding: 8px 12px !important;color: #333;border-bottom:1px solid #ddd;">Notice</h2>
		<div style="padding: 5px 15px">
			<p>Your campaign may have unsaved changes. Would you like to save it?</p>
			<button class="btn save_campaign_changes add">Save</button>
			<button class="btn discard_campaign_changes cancel">Cancel Changes</button>
			<a class="btn cancel cancel_campaign_changes" title="">Close</a>
		</div>
	</div>
	<input type="hidden" id="current_container_id" name="current_container_id">
  </div>
</div>
</div><!--Demo-QTips-->
<div id="myNicPanel"></div>

<div id="displaybox" style="display: none;"></div>
</body>
</html>
