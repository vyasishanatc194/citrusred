<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.fastconfirm.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/jquery.fastconfirm.css?v=6-20-13" media="screen" />
<script type="text/javascript">
    $(document).ready(function(){
      $(".fancybox").fancybox({'autoDimensions':false,'centerOnScroll':true,'scrolling':true,'width':'400','height':'230'});
    });
    function confirmExport(act, cid, sid){
      $(".export_csv").fastConfirm({
        position: "top",
        questionText: "Are you sure you want to export this list?",
        onProceed: function(trigger) {
          window.location = "<?php echo base_url();?>newsletter/emailreport_autoresponder/exportcsv/"+ act +'/'+cid+'/'+sid+'/';
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
      <div class="editing-theme-box <?php echo $current_tab == 'send_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport_autoresponder/view/sent/".$campaign_id);?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/sent/$campaign_id/$scheduled_id/");?>">Sent</a>
          </strong>
          <span class="right-no"><?php echo $autoresponder_report['total_delivered_emails'];?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'read_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport_autoresponder/view/read/$campaign_id/$scheduled_id/");?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/read/$campaign_id/$scheduled_id/");?>">Opened</a>
          </strong>
          <span class="right-no"><?php echo $autoresponder_report['total_read_emails'];?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'unread_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport_autoresponder/view/unread/$campaign_id/$scheduled_id/");?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/unread/$campaign_id/$scheduled_id/");?>">Unopened</a>
          </strong>
          <span class="right-no"><?php echo $autoresponder_report['total_unread_emails'];?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'bounced_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport_autoresponder/view/bounced/$campaign_id/$scheduled_id/");?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/bounced/$campaign_id/$scheduled_id/");?>">Bounced</a>
          </strong>
          <span class="right-no"><?php echo $autoresponder_report['total_bounce_emails'];?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'click_link' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport_autoresponder/view/click/$campaign_id/$scheduled_id/");?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/click/$campaign_id/$scheduled_id/");?>">Clicks</a>
          </strong>
          <span class="right-no"><?php echo $autoresponder_report['total_click_emails'];?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'forward_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport_autoresponder/view/forwardemail/$campaign_id/$scheduled_id/");?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/forwardemail/$campaign_id/$scheduled_id/");?>">Forwards</a>
          </strong>
          <span class="right-no"><?php echo $autoresponder_report['total_forward_emails'];?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'unsubscribes_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport_autoresponder/view/unsubscribes/$campaign_id/$scheduled_id/");?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/unsubscribes/$campaign_id/$scheduled_id/");?>">Unsubscribes</a>
          </strong>
          <span class="right-no"><?php echo $autoresponder_report['total_unsubscribes'];?></span>
        </div>
      </div>
      <div class="editing-theme-box <?php echo $current_tab == 'complaints_email' ? 'active' : '';?>" onclick="window.location='<?php echo  site_url("newsletter/emailreport_autoresponder/view/complaints/$campaign_id/$scheduled_id/");?>'">
        <div class="listname-no">
          <strong class="subscription_strong">
            <a href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/complaints/$campaign_id/$scheduled_id/");?>">Complaints</a>
          </strong>
          <span class="right-no"><?php echo $autoresponder_report['total_complaint_emails'];?></span>
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
		<a href="<?php echo CAMPAIGN_DOMAIN.'a/'.$campaign_id;?>" target="_blank" onMouseOver="this.style.textDecoration='underline'"  onMouseOut="this.style.textDecoration='none'">	
          <?php echo (trim($campaign_data['email_subject']) !='')?$campaign_data['email_subject']:$campaign_data['campaign_title']; ?>
		</a>  
           
		  <a class="fancybox add_to_list add btn" href="<?php  echo  site_url("newsletter/contacts/add_autoresponder_emailreport_to_contact_list/".$action."/$campaign_id/$scheduled_id"); ?>"> 
            <i class="icon-plus"></i>Add to List
          </a>
        </h2>
        
        <?php echo form_open('refer_friend/index', array('id' => 'frmReferFriend')); ?>
        <table class="list tbl-contacts" width="100%">
        <?php if(count($emailreport_data)>0){?>
                <tr>
                  <th colspan="2">
                    <strong>Link</strong>
                  </th>
                </tr>
                <?php foreach($emailreport_data as $click){ ?>
                  <tr>
                    <td>
                      <a href="<?php echo $click['actual_url']; ?>" target="_blank"><?php echo substr($click['actual_url'],0,70); ?></a>
                    </td>
                    <td>
                      <?php if($campaign_data['is_restore']==0){?>
						<a class="btn cancel" href="<?php echo  site_url("newsletter/emailreport_autoresponder/view/view_subscriber_click/".$click['campaign_id']."/".$scheduled_id."/0/".$click['tiny_url']);?>"><?php echo $click['cnt']; ?> Clicks</a>
                      <?php }else{ ?>
                        <?php echo $click['cnt']; ?> Clicks
                      <?php } ?>
                    </td>
                  </tr>
				  <?php }?>
				 <tr class="contacts_change">
				  <th colspan="2" class="export-container">

					 <a href="javascript:void(0);" onclick="javascript:confirmExport('<?php echo $action;?>','<?php echo $campaign_id;?>','<?php echo $scheduled_id;?>');" class="export_csv btn cancel">
					 <img src="<?php echo $this->config->item('webappassets');?>/images/table-export.png?v=6-20-13" alt="" align="absmiddle"> Export
					</a>
				  </th>
				</tr>
              <?php  }else{?>
                <tr><td>No record found</td></tr>
              <?php }?>
        </table>
      </div>
    </div>
  </div>
</div>
