<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list subtle">
    <?php
    //Fetch campaings from campaigns array
    if(count($campaign_data['campaigns'])) {
    $i=1;
    ?>
     <?php foreach($campaign_data['campaigns'] as $campaign){ ?>
  <tr id="campaign_<?php echo $campaign['campaign_id']; ?>">
    <td width="58%">
      <div><a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign['campaign_id'];?>" target="_blank" class="title">
        <?php echo ucfirst($campaign['campaign_title']);  ?>
      </a></div>
      <strong>Status: </strong>
      <?php
        // get campaign status
        if($campaign['campaign_status']=='draft'){
        echo ucfirst($campaign['campaign_status'])." <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y \a\t g:i a', strtotime( $campaign['draftDate']))." </a>"; //campaign not send
        }else if(($campaign['campaign_status']=='archived' or $campaign['campaign_status']=='queueing')&&(date('Y-m-d H:i:s', strtotime( $campaign['campaign_sheduled']))<date("Y-m-d H:i:s"))){
        echo "In Queue <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y', strtotime( $campaign['email_send_date']))." </a>"; //campaign not send
        }else if(($campaign['campaign_status']=='archived') ||($campaign['campaign_status']=='queueing') ||($campaign['campaign_status']=='active_ready')){
        echo "<span id='campaign_status_".$campaign['campaign_id']."'>Scheduled</span> <a style=\"cursor:text;\" href='javascript:void(0);'>". date('F j, Y \a\t g:i a', strtotime( $campaign['campaign_sheduled']))." </a><br/> <a href='javascript:void(0);' onclick='confirm_cancel_delivery(\"".$campaign['campaign_id']."\")' id='cancel_".$campaign['campaign_id']."'><strong>Cancel Delivery</strong></a>";  //campaign  scheduled
        }else if($campaign['campaign_status']=='active'){
        echo "Sent On <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y \a\t g:i a', strtotime( $campaign['email_send_date']))." </a>";  //campaign  sending
        }else if($campaign['campaign_status']=='ready'){
        echo "In Queue <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y', strtotime( $campaign['email_send_date']))." </a>";  //campaign  sending
        }else if($campaign['campaign_status']=='disallow'){
        echo "Suspended <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y', strtotime( $campaign['email_send_date']))." </a>";  //campaign  sending
        }else{
        echo ucfirst($campaign['campaign_status'])." <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y \a\t g:i a', strtotime( $campaign['campaign_date_added']))." </a>";
        }
       ?>
    </td>
    <td width="42%">
      <ul class="list-icons">
        <li <?php if($campaign['campaign_status']=='active'){ ?>class="social_share_link" <?php } ?>>
          <?php if((($campaign['campaign_status']=='archived')&&(date('Y-m-d H:i:s', strtotime( $campaign['campaign_sheduled']))<date("Y-m-d H:i:s")))||($campaign['campaign_status']=='ready')){ ?>
            <a href='javascript:void(0);' onclick="fancyAlert('This email campaign is already in queue and will be sent shortly.')" title="Send" class="btn cancel"><i class="icon-envelope"></i></a>
          <?php }else if(($campaign['campaign_status']!='active')&&($campaign['campaign_status']!='ready')&&($campaign['campaign_status']!='unapproved')){ ?>
            <a <?php if($campaign_data['upgrade_package']==1) {?>onclick="fancyAlert('You are over your current plan limit. Please Upgrade Now.');" <?php } ?> href='javascript:void(0);' onclick="javascript:window.location.href='<?php echo  site_url("newsletter/campaign_email_setting/index/".$campaign['campaign_id']);?>';" title="Send" class="btn cancel"><i class="icon-envelope"></i></a>
          <?php }else if($campaign['campaign_status']=='active'){
			   $encoded_url = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode(CAMPAIGN_DOMAIN.'c/'.$campaign_data['campaign_id']).'';

		  ?>
          <!--  <a href="http://www.facebook.com/share.php?u=<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign['campaign_id'];?>&t=<?php echo $campaign['email_subject']?>" title="Click to share this post on Facebook" target="_blank" class="btn cancel"><i class="icon-facebook"></i></a>
			-->
			   <a href="<?php echo $encoded_url;?>" title="Click to share this post on Facebook" target="_blank" class="btn cancel"><i class="icon-facebook"></i></a>
			
	   </li>
        <li>
            <a href="http://twitter.com?status=Here is our newest campaign : <?php echo CAMPAIGN_DOMAIN.'c/'.$campaign['campaign_id'];?> via RedCappi" title="Click to share this post on Twitter" target="_blank" class="btn cancel"><i class="icon-twitter"></i></a>
          <?php }else {?>
            <a href='javascript:void(0);' onclick="fancyAlert('To re-send this campaign, click the copy button and this campaign will be replicated')" title="Send" class="btn cancel"><i class="icon-envelope"></i></a>
          <?php } ?>
        </li>
      <?php if($campaign['campaign_status']!='active'){?>
        <?php if((($campaign['campaign_status']=='archived')&&(date('Y-m-d H:i:s', strtotime( $campaign['campaign_sheduled']))<date("Y-m-d H:i:s")))||($campaign['campaign_status']=='ready')){ ?>
          <li><a href="javascript:void(0);" onclick="fancyAlert('To edit, first click copy button to replicate this campaign, then edit and save changes');"  title="Edit" class="btn cancel" ><i class="icon-pencil"></i></a></li>
        <?php }else if(($campaign['campaign_status']!='active')&&($campaign['campaign_status']!='ready')&&($campaign['campaign_status']!='unapproved')){
          if($campaign['campaign_template_option']==3){?>
          <li><a href="javascript:void(0);" onclick="window.location.href='<?php echo site_url('newsletter/campaign/campaign_editor/'.$campaign['campaign_id']);?>'"  title="Edit" class="btn cancel"><i class="icon-pencil"></i></a></li>
          <?php }else{ ?>
          <li><a href="javascript:void(0);" onclick="window.location.href='<?php echo  site_url('newsletter/campaign_template_options/index/'.$campaign['campaign_id']);?>'" title="Edit" class="btn cancel"><i class="icon-pencil"></i></a></li>
        <?php }}else{ ?>
          <li><a href="javascript:void(0);" onclick="fancyAlert('To edit, first click copy button to replicate this campaign, then edit and save changes');" title="Edit" class="btn cancel"><i class="icon-pencil"></i></a></li>
        <?php } ?>
      <?php } ?>
      <li>
        <a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign['campaign_id'];?>" title="view" target="_blank" class="btn cancel"><i class="icon-eye-open"></i></a>
      </li>
      <li>
        <a href="<?php echo  site_url('newsletter/campaign/copy_archived/'.$campaign['campaign_id']);?>" title="Copy" class="btn cancel"><i class="icon-copy"></i></a>
      </li>
      <?php if($campaign['campaign_status']=='active' or $campaign['campaign_status']=='disallow'){ ?>
      <li><a href="javascript:void(0);" onclick="window.location.href='<?php echo site_url("newsletter/emailreport/display/".$campaign['campaign_id']);?>';"  title="Stats" class="btn cancel"><i class="icon-bar-chart"></i></a></li>
      <?php }else{ ?>
      <li><a href="javascript:void(0);" onclick="fancyAlert(' Nothing to track. Campaign has not been sent.')" title="Stats" class="btn cancel"><i class="icon-bar-chart"></i></a></li>
      <?php } ?>
      <li><a class="delete-row btn cancel"  title="Delete" name="<?php echo $campaign['campaign_id']; ?>" ><i class="icon-trash"></i></a></li>
    </ul>
  </td>
  </tr>
  <?php $i++; } ?>
  <?php }else{  //record not found ?>
  <tr class="clean">
    <td>
      <div class="empty">
        <p>To begin click "New Campaign" button and create your first email campaign promotion.</p>
        <a href="<?php echo  site_url("newsletter/campaign/create");?>" class="btn add"><i class="icon-plus"></i>New Campaign</a>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>
<!--Display paging links -->
<div class="pagination-container"><?php echo $paging_links ?></div>
