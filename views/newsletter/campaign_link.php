<?php
	if($email_subject !="")
		$subject=$email_subject;
	else
	$subject=$campaign_title;
	$subject=str_replace('$', '&#36;',$subject);
	
	$title="<title>$subject</title>\n";
	$meta=	'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n".
			'<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />'."\n".
			'<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';
	
	
	
	$css='<link href="'.$this->config->item("webappassets").'/css/email_preview.css?v=6-20-13" rel="stylesheet"></link>'."\n";
	$js='<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>'."\n".
		'<script type="text/javascript">stLight.options({publisher: "ur-eca47de6-bbd8-292f-ea06-d74b8874e989", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>';
	
	$fb_meta_data = $this->is_authorized->getOGMetaTags($campaign_content, "$subject");
	
	if (stripos($campaign_content, '<head>') !== false) {
		$campaign_content=str_replace("<head>","<head>".$title . $meta . $fb_meta_data . $css . $js,$campaign_content);	
	}else{		
		$campaign_content = preg_replace('/<body(\s[^>]*)?>/i', '<head>'.$title . $meta . $fb_meta_data . $css . $js.'</head><body\\1>', $campaign_content);  
	}
	$campaign_content=str_replace("<title>RedCappi Campaign</title>","", $campaign_content);	
	
	$topBar = '<div id="diy-header-container" class="rcemail">
					<div id="diy-header" class="rcemail">
						<div id="share-this" class="rcemail">
							<span class="st_facebook_hcount" displayText="Facebook"></span>
							<span class="st_twitter_hcount" displayText="Tweet"></span>
							<span class="st_linkedin_hcount" displayText="LinkedIn"></span>
							<span class="st_pinterest_hcount" displayText="Pinterest"></span>
						</div>
						<strong>'.$subject.'</strong>
					</div>
				</div>';
	
 $campaign_content = preg_replace('/<body(\s[^>]*)?>/i', '<body\\1>'.$topBar, $campaign_content, 1);
 //$campaign_content = str_replace('https://redcappi.com','http://redcappi.com',$campaign_content);	
 //$campaign_content = hyperlinksAnchored($campaign_content);

	if($campaign_template_option==5){
		$c = '<pre>'.htmlspecialchars($campaign_content).'</pre>';
	}else if(($campaign_template_option==4)||($campaign_template_option==2)){
		$c = html_entity_decode($campaign_content, ENT_QUOTES, "utf-8" );
	}else if($campaign_template_option==1){
		$c = htmlspecialchars_decode ($campaign_content);
	}else{
		$c = $campaign_content;
	}
	echo ($c);
/**
 * Replace links in text with html links
 *
 * @param  string $text
 * @return string
 */
 function hyperlinksAnchored($text) {
    return preg_replace('@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@', 'http$2://$4', $text);
}
?>


<?php if($rc_logo==1){ 
		//echo '<div class="footlink">Powered by <a href="http://www.'.SYSTEM_DOMAIN_NAME.'" target="_blank">RedCappi</a></div>';	
		echo '<div id="footer-logo" class="rcemail"><a href="'.site_url("/").'"> <img src="'. $this->config->item('webappassets').'images-front/thanks-logo.png?v=6-20-13" alt="logo" title="logo" border="0" /></a></div>';
} ?>

</body>
</html>
