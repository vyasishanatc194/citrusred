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
	 
	
	if (stripos($campaign_content, '<head>') !== false) {
		$campaign_content=str_replace("<head>","<head>".$title . $meta  . $css ,$campaign_content);	
	}else{		
		$campaign_content = preg_replace('/<body(\s[^>]*)?>/i', '<head>'.$title . $meta .  $css . '</head><body\\1>', $campaign_content);  
	}
	$campaign_content=str_replace("<title>RedCappi Campaign</title>","", $campaign_content);	
	
	 


	if($campaign_template_option==5){
		echo '<pre>'.htmlspecialchars($campaign_content).'</pre>';
	}else if(($campaign_template_option==4)||($campaign_template_option==2)){
		echo html_entity_decode($campaign_content, ENT_QUOTES, "utf-8" );
	}else if($campaign_template_option==1){
		echo htmlspecialchars_decode ($campaign_content);
	}else{
		echo $campaign_content;
	}
?>

 

</body>
</html>
