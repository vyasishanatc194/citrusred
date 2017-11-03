<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.fastconfirm.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/jquery.fastconfirm.css?v=6-20-13" media="screen" />
<script type="text/javascript">
    $(document).ready(function(){
      $(".fancybox").fancybox({'autoDimensions':false,'centerOnScroll':true,'scrolling':true,'width':'400','height':'230'});
    });
    function confirmExport(act, cid){
      $(".export_csv").fastConfirm({
        position: "top",
        questionText: "Are you sure you want to export this list?",
        onProceed: function(trigger) {
          window.location = "<?php echo base_url();?>newsletter/emailreport/exportcsv/"+ act +'/'+cid+'/';
        },
        onCancel: function(trigger) {
        }
      });
    }
</script>
<!--[body]-->
<div id="body-dashborad" class="nobackground">
  <div class="container">
    <h1>Stats</h1>
    <div class="left-menu contacts stats">
      <div class="editing-theme-box <?php echo $current_tab == 'send_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport/view/sent/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport/view/sent/".$campaign_id);?>">Sent</a>
          </strong>
          <span class="right-no"><?php echo $sent_total_count;?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'read_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport/view/read/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport/view/read/".$campaign_id);?>">Opened</a>
          </strong>
          <span class="right-no"><?php echo $read_total_count;?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'unread_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport/view/unread/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport/view/unread/".$campaign_id);?>">Unopened</a>
          </strong>
          <span class="right-no"><?php echo $unread_total_count;?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'bounced_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport/view/bounced/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport/view/bounced/".$campaign_id);?>">Bounced</a>
          </strong>
          <span class="right-no"><?php echo $bounced_total_count;?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'click_link' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport/view/click/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport/view/click/".$campaign_id);?>">Clicks</a>
          </strong>
          <span class="right-no"><?php echo $clicks_total_count;?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'forward_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport/view/forwardemail/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport/view/forwardemail/".$campaign_id);?>">Forwards</a>
          </strong>
          <span class="right-no"><?php echo $forward_total_count;?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'unsubscribes_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport/view/unsubscribes/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport/view/unsubscribes/".$campaign_id);?>">Unsubscribes</a>
          </strong>
          <span class="right-no"><?php echo $unsubscribes_total_count;?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'complaints_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport/view/complaints/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport/view/complaints/".$campaign_id);?>">Complaints</a>
          </strong>
          <span class="right-no"><?php echo $complaints_total_count;?></span>
        </div>
      </div>
      <div class="backdrop"></div>
    </div>
    <div class="right-menu contacts stats">
      <?php if(validation_errors()){
          echo '<div style="color:#FF0000;" class="msg">'.validation_errors().'</div>';
        }
        // display all messages
        if (is_array($messages)):
          echo '<div class="info" style="background:none; border:none;">';
          foreach ($messages as $type => $msgs):
            foreach ($msgs as $message):
              echo ('<span class="' .  $type .'">' . $message . '</span>');
            endforeach;
          endforeach;
          echo '</div>';
        endif;
      ?>
      <div class="emailreport_list">
        <h2 class="list_title">
		<a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign_id;?>" target="_blank" onMouseOver="this.style.textDecoration='underline'"  onMouseOut="this.style.textDecoration='none'"><?php echo (trim($campaign_data['email_subject']) !='')?$campaign_data['email_subject']:$campaign_data['campaign_title']; ?></a>          
           <a class="fancybox add_to_list add btn" href="<?php echo site_url("newsletter/contacts/add_emailreport_to_contact_list/".trim($this->uri->segment(4))."/".$campaign_id);?>">		   
            <i class="icon-plus"></i>Add to List
          </a>
        </h2>
        <strong>Sent at:</strong>
        <span><?php 		
		echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($campaign_data['email_send_date'], $this->session->userdata('member_time_zone')))); 
		?>		
		</span>
        <?php echo form_open('refer_friend/index', array('id' => 'frmReferFriend')); ?>
        <table class="list tbl-contacts" width="100%">
        <?php if(count($emailreport_data)>0){?>
                <tr>
                  <th colspan="2">
                    <strong>Link</strong>
                  </th>
                </tr>
				 <?php 
					if($campaign_data['is_restore']==0){
							foreach($emailreport_data as $click){ ?>
							  <tr>
								<td><a href="<?php echo $click['actual_url']; ?>" target="_blank"><?php echo substr($click['actual_url'],0,70); ?></a></td>
								<td><a class="btn cancel" href="<?php echo  site_url("newsletter/emailreport/view_subscriber_click/".$click['campaign_id']."/".$click['tiny_url']);?>"><?php echo $click['cnt']; ?> Clicks</a></td>
							  </tr>
				   <?php 	}
				   }else{ 
						usort($emailreport_data, function($a, $b) {
						//return $a['actual_url'] - $b['actual_url'];
						if ($a['actual_url'] < $b['actual_url']) {
							return -1;
						} else if ($a['actual_url'] > $b['actual_url']) {
							return 1;
						} else {
							return 0;
						}
						});	
						$strActualURL = '';
						$intCounter =0;
						foreach($emailreport_data as $click){ 
							$intCounter 	+= $click['counter'];
							if($strActualURL == ''){	
								$strActualURL 	= $click['actual_url'];								
							}elseif($strActualURL != $click['actual_url']){	
								echo "<tr><td><a href='$strActualURL' target='_blank'>". substr($strActualURL,0,70)."</a></td><td>{$intCounter} Clicks</td></tr>";
								$strActualURL 	= $click['actual_url'];								
								$intCounter = 0;																
							}								
						}
						echo "<tr><td><a href='$strActualURL' target='_blank'>". substr($strActualURL,0,70)."</a></td><td>{$intCounter} Clicks</td></tr>";
				    } ?>
				  <tr class="contacts_change">
				  <th colspan="2" class="export-container">

					 <a href="javascript:void(0);" onclick="javascript:confirmExport('<?php echo trim($this->uri->segment(4));?>',<?php echo $campaign_id;?>);" class="export_csv btn cancel">
					 <img src="<?php echo $this->config->item('webappassets');?>/images/table-export.png?v=6-20-13" alt="" align="absmiddle"> Export
					</a>
				  </th>
				</tr>
              <?php }else{?>
                <tr><td>No record found</td></tr>
              <?php }?>
        </table>
      </div>
    </div>
  </div>
</div>
