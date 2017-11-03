<?php
 echo '<?xml version="1.0" encoding="'.$encoding.'"?>' . "\n";
?> 
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:admin="http://webns.net/mvcb/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
		<title><?php echo $feed_name; ?></title>
		<copyright>Copyright (c) 2016 Redcappi LLC, All rights reserved.</copyright>
		<link><?php echo $feed_url; ?></link>
		<?php if($category_id>0){?>
			<category><?php echo htmlentities($category_name); ?></category>
		<?php } ?>
		<description><?php echo htmlentities($page_description); ?></description>
		<dc:language><?php echo $page_language; ?></dc:language>
	   <image>
			<title><?php echo $image_title; ?></title>
			<width><?php echo $image_width; ?></width>
			<height><?php echo $image_height; ?></height>
			<link><?php echo $site_url; ?></link>
			<url><?php echo $image_url; ?></url>
		</image>
		<?php foreach($posts->result() as $entry): ?>
		<?php $title=preg_replace("![^a-z0-9]+!i", "-", trim($entry->title)); ?>
        <item>
			<title><?php echo xml_convert($entry->title); ?></title>
			<link><?php echo site_url('blog/'.$title .'/' . $entry->id); ?></link>
			<guid><?php echo site_url('blog/'.$title .'/' . $entry->id); ?></guid>
			<category><?php echo htmlentities($entry->category_name); ?></category>
			<pubDate>
				<?php
					$timestamp=strtotime($entry->added_on);
					echo date('r', $timestamp);
				?>
			</pubDate>
			<description>
				<![CDATA[
					<?php echo str_replace('/img/post_resources/', base_url() . 'img/post_resources/', str_replace("http://www.".SYSTEM_DOMAIN_NAME."/", base_url(),str_replace("../../../../", base_url(),$entry->desc))); ?>
				]]>
			</description>			
        </item>
		<?php endforeach; ?>    
    </channel>
</rss>