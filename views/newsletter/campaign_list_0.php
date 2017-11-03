<script type="text/javascript">
/*
  fancyAlert to display message
*/
function fancyAlert(msg) {
  var msg="<h5>Confirm</h5><p>" + msg + "</p><div class='btn-group'><button class='btn confirm fast_confirm_cancel' onclick='$.fancybox.close()'>Ok</button></div>"
  $.fancybox({
  'content' : "<div style=\"width:400px;\">"+msg+"</div>"
  });
}
/*
  ajax call to delete contact
*/
jQuery(".delete-row").live('click',function(event) {
  var msg='<h5>Confirm</h5><p>Are you sure you want to delete this campaign? All information associated with this campaign, including stats, will be permanently deleted!</p><div class="btn-group"><button class="btn danger fast_confirm_proceed" onclick="deleteCampaign(\''+jQuery(this).attr('name')+'\')">Delete</button><button class="btn cancel fast_confirm_cancel">No</button></div>';
  $.fancybox({'content' : "<div style=\"width:400px;\">"+msg+"</div>"});
});
function confirm_cancel_delivery(campaign_id){
  var msg='<h5>Confirm</h5><p>Are you sure you want to cancel your scheduled email campaign and revert back to draft mode.</p><button class="btn danger fast_confirm_proceed" onclick="cancel_delivery(\''+campaign_id+'\')">Yes</button><button class="btn cancel fast_confirm_cancel">No</button>';
  $.fancybox({'content' : "<div style=\"width:400px;\">"+msg+"</div>"});
}
function cancel_delivery(campaign_id){
  jQuery.ajax({
  url: "<?php echo base_url() ?>newsletter/campaign/cancel_campaign_delivery/"+campaign_id,
  type:"POST",
  success: function(data){
    jQuery("#campaign_status_"+campaign_id).html('Canceled');
    jQuery("#cancel_"+campaign_id).remove();
    jQuery("#campaign_send_"+campaign_id).attr('onclick','').click(function(){window.location.href='<?php echo base_url() ?>newsletter/campaign_email_setting/index/'+ campaign_id;});
    jQuery("#campaign_edit_"+campaign_id).attr('onclick','').click(function(){window.location.href='<?php echo base_url() ?>newsletter/campaign/campaign_editor/'+ campaign_id;});
    $.fancybox.close();
  }});
}
$('#fancybox-wrap').find('.fast_confirm_cancel').live('click',function(){
  $.fancybox.close();
});
/*
  ajax call to delete campaign
*/
function deleteCampaign(campaign_id){
  jQuery.ajax({
  url: "<?php echo base_url() ?>newsletter/campaign/delete/"+campaign_id,
  type:"POST",
  success: function(data){
    jQuery("#campaign_"+campaign_id).remove();
    $.fancybox.close();
  }});
}
function openDiv(){
  document.getElementById("topnav").innerHTML='<a  href="#" class="signin" onclick="return closeDiv();"><span>Login</span></a>';
  $("#topnav a").addClass("menu-open");
  $("#signin_menu").slideDown("slow");
}
function closeDiv(){
  document.getElementById("topnav").innerHTML='<a  href="#" class="signin " onclick="return openDiv();"><span>Login</span></a>';
  $("#topnav a").removeClass("menu-open");
  $("#signin_menu").slideUp("slow");
}
</script>
<div id="body-dashborad">
  <div class="container">
    <h1>
		<a href="<?php echo  site_url("newsletter/campaign/create");?>" class="btn add dropdown">
          <i class="icon-plus"></i>New Campaign
        </a> 
      Campaigns
    </h1>
    <?php
    // display all messages
    if (is_array($campaign_data['messages'])){
      echo '<div class="info">';
      foreach ($campaign_data['messages'] as $type => $msgs):
      foreach ($msgs as $message):
        echo ('<span class="' .  $type .'">' . $message . '</span>');
      endforeach;
      endforeach;
      echo '</div>';
    }elseif(($campaign_data['active_campaign_count']>0)||($campaign_data['ready_campaign_count']>0)||($campaign_data['queueing_campaign_count']>0)){
      echo '<div class="info">Your email campaign is in our sending queue.<br/>Sending may take a bit longer, depending on the number of emails queued before yours.</div>';
    }
    ?>
    <div class="inner-container">
      <div id="list-container">
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
					echo ucfirst($campaign['campaign_status'])." <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y \a\t g:i a', strtotime($campaign['draftDate']))." </a>"; //campaign not sent
                }elseif(($campaign['campaign_status']=='archived' or $campaign['campaign_status']=='queueing')&&(date('Y-m-d H:i:s', strtotime( $campaign['campaign_sheduled']))<date("Y-m-d H:i:s"))){
					echo "In Queue <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y', strtotime( $campaign['email_send_date']))." </a>"; //campaign not sent yet
                }elseif(($campaign['campaign_status']=='archived') ||($campaign['campaign_status']=='queueing') ||($campaign['campaign_status']=='active_ready')){
					echo "<span id='campaign_status_".$campaign['campaign_id']."'>Scheduled</span> <a style=\"cursor:text;\" href='javascript:void(0);'>". date('F j, Y \a\t g:i a', strtotime( $campaign['campaign_sheduled']))." </a><br/> <a href='javascript:void(0);' onclick='confirm_cancel_delivery(\"".$campaign['campaign_id']."\")' id='cancel_".$campaign['campaign_id']."'><strong>Cancel Delivery</strong></a>";  //campaign  scheduled
                }elseif($campaign['campaign_status']=='active'){
					echo "Sent On <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y \a\t g:i a', strtotime( $campaign['email_send_date']))." </a>";  //campaign sent
                }elseif($campaign['campaign_status']=='ready'){
					echo "In Queue <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y  \a\t g:i a', strtotime( $campaign['email_send_date']))." </a>";  //campaign  waiting admin approval
                }elseif($campaign['campaign_status']=='disallow'){
					echo "Suspended <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y', strtotime( $campaign['email_send_date']))." </a>";  //campaign disallowed by admin
                }else{
					echo ucfirst($campaign['campaign_status'])." <a style=\"cursor:text;\" href='javascript:void(0);'>".date('F j, Y \a\t g:i a', strtotime( $campaign['campaign_date_added']))." </a>";
                }
               ?>
            </td>
            <td width="42%">
              <ul class="list-icons">
                <li <?php if($campaign['campaign_status']=='active'){ ?>class="social_share_link" <?php } ?>>
                  <?php if((($campaign['campaign_status']=='archived')&&(date('Y-m-d H:i:s', strtotime( $campaign['campaign_sheduled'])) < date("Y-m-d H:i:s")))||($campaign['campaign_status']=='ready')||($campaign['campaign_status']=='queueing')){ ?>
                    <a href='javascript:void(0);'  id='campaign_send_<?php echo $campaign['campaign_id']?>' onclick="fancyAlert('This email campaign is already in queue and will be sent shortly.')" title="Send" class="btn cancel"><i class="icon-envelope"></i></a>
				  <?php }elseif(($campaign['campaign_status']!='draft') && ($campaign['campaign_status']!='active') && ($campaign['campaign_status']!='disallow') && date('Y-m-d H:i:s', strtotime( $campaign['campaign_sheduled'])) > date("Y-m-d H:i:s")){ ?>
                    <a href='javascript:void(0);' id='campaign_send_<?php echo $campaign['campaign_id']?>' onclick="fancyAlert('This email campaign is already in queue and will be sent at scheduled time. To re-schedule, first click Cancel Delivery and then schedule again.')" title="Send" class="btn cancel"><i class="icon-envelope"></i></a>
                  <?php }elseif(($campaign['campaign_status']!='active')&&($campaign['campaign_status']!='ready')&&($campaign['campaign_status']!='unapproved')){ ?>
                    <a href='javascript:void(0);' id='campaign_send_<?php echo $campaign['campaign_id']?>' <?php if($campaign_data['upgrade_package']==1) {?>onclick="fancyAlert('You are over your current plan limit. Please Upgrade Now.');" <?php }else{ ?>  onclick="javascript:window.location.href='<?php echo  site_url("newsletter/campaign_email_setting/index/".$campaign['campaign_id']);?>';" <?php } ?> title="Send" class="btn cancel"><i class="icon-envelope"></i></a>
                  <?php }elseif($campaign['campaign_status']=='active'){ ?>
                    <a href="http://www.facebook.com/share.php?u=<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign['campaign_id'];?>&t=<?php echo $campaign['email_subject']?>" title="Click to share this post on Facebook" target="_blank" class="btn cancel"><i class="icon-facebook"></i></a>
                </li>
                <li>
                    <a href="http://twitter.com?status=Here is our newest campaign : <?php echo CAMPAIGN_DOMAIN.'c/'.$campaign['campaign_id'];?> via RedCappi" title="Click to share this post on Twitter" target="_blank" class="btn cancel"><i class="icon-twitter"></i></a>
                  <?php }else{?>
                    <a href='javascript:void(0);' onclick="fancyAlert('To re-send this campaign, click the copy button and this campaign will be replicated')" title="Send" class="btn cancel"><i class="icon-envelope"></i></a>
                  <?php } ?>
                </li>
              <?php if($campaign['campaign_status']!='active'){
					if((($campaign['campaign_status']=='archived')&&(date('Y-m-d H:i:s', strtotime( $campaign['campaign_sheduled']))<date("Y-m-d H:i:s")))||($campaign['campaign_status']=='ready')){ ?>
					  <li><a href="javascript:void(0);" onclick="fancyAlert('To edit, first click copy button to replicate this campaign, then edit and save changes');"  title="Edit" class="btn cancel" ><i class="icon-pencil"></i></a></li>
					<?php }elseif(($campaign['campaign_status']!='draft') && ($campaign['campaign_status']!='active') && date('Y-m-d H:i:s', strtotime( $campaign['campaign_sheduled'])) > date("Y-m-d H:i:s")){ ?>
						<li><a href="javascript:void(0);" id='campaign_edit_<?php echo $campaign['campaign_id']?>' onclick="fancyAlert('This email campaign is already in queue and will be sent at scheduled time. To edit, first click Cancel Delivery and then edit campaign.');"  title="Edit" class="btn cancel" ><i class="icon-pencil"></i></a></li>
					<?php
					}elseif(($campaign['campaign_status']!='active')&&($campaign['campaign_status']!='ready')&&($campaign['campaign_status']!='unapproved')){
							if($campaign['campaign_template_option']==3){?>
							  <li><a href="javascript:void(0);" onclick="window.location.href='<?php echo site_url('newsletter/campaign/campaign_editor/'.$campaign['campaign_id']);?>'"  title="Edit" class="btn cancel"><i class="icon-pencil"></i></a></li>
					<?php 	}else{ ?>
							  <li><a href="javascript:void(0);" onclick="window.location.href='<?php echo  site_url('newsletter/campaign_template_options/index/'.$campaign['campaign_id']);?>'" title="Edit" class="btn cancel"><i class="icon-pencil"></i></a></li>
					<?php
							}
					}else{ ?>
					  <li><a href="javascript:void(0);" onclick="fancyAlert('To edit, first click copy button to replicate this campaign, then edit and save changes');" title="Edit class="btn cancel""><i class="icon-pencil"></i></a></li>
			<?php 	}
				} ?>
              <li>
                <a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign['campaign_id'];?>" title="view" target="_blank" class="btn cancel"><i class="icon-eye-open"></i></a>
              </li>
              <li>
                <a href="<?php echo  site_url('newsletter/campaign/copy_archived/'.$campaign['campaign_id']);?>" title="Copy" class="btn cancel"><i class="icon-copy"></i></a>
              </li>
              <?php if($campaign['campaign_status']=='active' or $campaign['campaign_status']=='disallow'){ ?>
              <li><a href="javascript:void(0);" onclick="window.location.href='<?php echo site_url("newsletter/emailreport/display/".$campaign['campaign_id']);?>';"  title="Stats" class="btn cancel"><i class="icon-bar-chart"></i></a></li>
              <?php }else{ ?>
              <li><a href="javascript:void(0);" onclick="fancyAlert('Nothing to track. Campaign has not been sent.')" title="Stats" class="btn cancel"><i class="icon-bar-chart"></i></a></li>
              <?php } ?>
              <li><a class="delete-row btn cancel"  title="Delete" name="<?php echo $campaign['campaign_id']; ?>" ><i class="icon-trash"></i></a></li>
            </ul>
          </td>
          </tr>
          <?php $i++; } ?>
          <?php }else{  //record not found ?>
          <tr class="clean">
            <td>
              <div class="empty" style="background: url('<?php echo $this->config->item('webappassets');?>images/campaign-preview.jpg') no-repeat center; height: 400px">
                <p style="padding-top: 100px">To begin click "New Campaign" button and create your first email campaign promotion.</p>
                <a href="<?php echo  site_url("newsletter/campaign/create");?>" class="btn add"><i class="icon-plus"></i>New Campaign</a>
              </div>
            </td>
          </tr>
          <?php } ?>
        </table>
        <!--Display paging links -->
        <div class="pagination-container"><?php echo $paging_links ?></div>
      </div>
    </div>
  </div>
</div>
<!--[/body]-->