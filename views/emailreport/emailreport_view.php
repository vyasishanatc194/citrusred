<script type="text/javascript">
    $(document).ready(function(){
    $(".fancybox").fancybox({'autoDimensions':false,'centerOnScroll':true,'scrolling':true,'width':'400','height':'auto'});
    });
  function fancyAlert(msg){
    $.fancybox({
    'content' : "<div style='width:400px;'><h5>Alert</h5><p style='text-align:left'>"+msg+"</p><div class='btn-group'><span class='btn cancel' onclick='$.fancybox.close();'>Close</span></div></div>"
    });
  }
</script>
 <?php if(count($emailreport_data)>0){ ?>
 <!--[if IE]><script language="javascript" type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/excanvas.js?v=6-20-13"></script><![endif]-->
 <!--[body]-->
   <!-- BEGIN: load jquery -->
  <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/jquery.jqplot.css?v=6-20-13" />
  <!-- END: load jquery -->

  <!-- BEGIN: load jqplot -->
<script language="javascript" type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.jqplot.js?v=6-20-13"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jqplot.pieRenderer.js?v=6-20-13"></script>
  <!-- END: load jqplot -->
<script id="example_1" type="text/javascript">
    $(document).ready(function(){
    <?php 
	$campaignId = array();
	$campaignAbId = array();
	foreach($emailreport_data as $key=>$email){ 
		if(!in_array($key,$campaignId)){
			$campaignId[$key] = $key;
			$campaignPie = "piecontainer_".$key;
		}else{
			$campaignPie = "piecontainer_exist".$key;
		}

	?>
    <?php if($email['total_delivered_emails']>0){ ?>
    s1 = [['Opened',<?php echo $email['per_read_emails']; ?>], ['Unopened', <?php echo $email['per_unread_emails']; ?>], ['Bounced', <?php echo $email['per_bounce_emails']; ?>]];
    plot1 = $.jqplot('<?php echo $campaignPie; ?>', [s1], {
        grid: {
            drawBorder: false,
            drawGridlines: false,
            background: '#ffffff',
            shadow: false
        },
        axesDefaults: {

        },
        seriesDefaults:{
            renderer:$.jqplot.PieRenderer,
            shadow: false,
            fontFamily: "arial",
            seriesColors: ["#3498DB","#F1C40F","#E67E22","#34495E","#1ABC9C"],
            rendererOptions: {
                showDataLabels: true
            }
        },
        legend: {
            show: true,
            location: 'e',
            border: '0',
            fontFamily: "arial",
            rowSpacing: '10px',
            marginRight: '10px',
            fontSize: '13px'
        }
    });
<?php }else{ ?>
$('#piecontainer_<?php echo $key; ?>').html("No Report");
	<?php }
	if	(!empty($email['is_ab'])){
		
		$refrenseCampignId = $email['is_ab']['refrense_campaign'];
		$refrenseCampign = $email['is_ab'][$refrenseCampignId];
		if(!in_array($refrenseCampignId,$campaignAbId)){
			$campaignAbId[$refrenseCampignId] = $refrenseCampignId;
			$campaignAbPie = "piecontainer_abtesting".$refrenseCampignId;
		}else{
			$campaignAbPie = "piecontainer_abtesting_exist".$refrenseCampignId;
		}
	if($refrenseCampign['total_delivered_emails']>0){ ?>
    s1 = [['Opened',<?php echo $refrenseCampign['per_read_emails']; ?>], ['Unopened', <?php echo $refrenseCampign['per_unread_emails']; ?>], ['Bounced', <?php echo $refrenseCampign['per_bounce_emails']; ?>]];
    plot1 = $.jqplot('<?php echo $campaignAbPie; ?>', [s1], {
        grid: {
            drawBorder: false,
            drawGridlines: false,
            background: '#ffffff',
            shadow: false
        },
        axesDefaults: {

        },
        seriesDefaults:{
            renderer:$.jqplot.PieRenderer,
            shadow: false,
            fontFamily: "arial",
            seriesColors: ["#3498DB","#F1C40F","#E67E22","#34495E","#1ABC9C"],
            rendererOptions: {
                showDataLabels: true
            }
        },
        legend: {
            show: true,
            location: 'e',
            border: '0',
            fontFamily: "arial",
            rowSpacing: '10px',
            marginRight: '10px',
            fontSize: '13px'
        }
    });
<?php }else{ ?>
$('#piecontainer_abtesting<?php echo $refrenseCampignId; ?>').html("No Report");
	<?php }
	}	
	}
	?>
});
</script>
<?php } ?>
  <div id="body-dashborad" class="nobackground">
    <div class="container">
      <h1>Stats</h1>
      <?php if(count($emailreport_data)>0){
		$campaignId = array();
		$campaignAbId = array();
		
	  ?>
      <?php foreach($emailreport_data as $key=>$email){ 
		if(!in_array($key,$campaignId)){
			$campaignId[$key] = $key;
			$campaignPie = "piecontainer_".$key;
		}else{
			$campaignPie = "piecontainer_exist".$key;
		}
	  
	  ?>
	  
        <table class="report">
			<tr>
				<?php if(!empty($email['is_ab'])){
					$refrenseCampignId = $email['is_ab']['refrense_campaign'];
					if(!in_array($refrenseCampignId,$campaignAbId)){
						$campaignAbId[$refrenseCampignId] = $refrenseCampignId;
						$campaignAbPie = "piecontainer_abtesting".$refrenseCampignId;
					}else{
						$campaignAbPie = "piecontainer_abtesting_exist".$refrenseCampignId;
					}
					?>
					<td  style="padding: 0px !important;"><h2><?php echo $email['campaign_title']; ?></h2></td>
					<td style="padding: 0px !important;"><h2><?php echo $email['is_ab'][$refrenseCampignId]['campaign_title']; ?></h2></td>
				<?php }else{ ?>
					<td colspan="2" style="padding: 0px !important;"><h2><?php echo $email['campaign_title']; ?></h2></td>
					
				<?php } ?>
			</tr>
			<?php if(!empty($email['is_ab'])) { ?>
			<tr>
				<td <?php if(!empty($email['is_ab'])) { echo 'width="50%"';}else{ echo 'width="30%"'; } ?> >
              <div style="height: 295px; overflow: hidden;">
                <div id="piecontainer_<?php echo $key; ?>" style="height: 300px; overflow: hidden;"></div>
              </div>
            </td>
			<?php if(!empty($email['is_ab'])) { ?>
			<td width="50%">
              <div style="height: 295px; overflow: hidden;">
                <div id="<?php echo $campaignAbPie; ?>" style="height: 300px; overflow: hidden;"></div>
              </div>
            </td>
			<?php } ?>
			</tr>
			<?php } ?>
          <tr valign="top">
            <td <?php if(!empty($email['is_ab'])) { echo 'width="50%"';}else{ echo 'width="70%"'; } ?> >
              <table class="stats">
                <tr><td><strong>Sent at:</strong> <span><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($email['email_send_date'], $this->session->userdata('member_time_zone')) ))?></span></td></tr>
                <tr><td><strong>From:</strong> <span><?php echo ucfirst($email['sender_name'])." &lt;"; ?><?php echo $email['sender_email']."&gt;"; ?></span></td></tr>
                <tr><td><strong>List:</strong> <span><?php echo ucfirst($email['subscription_list_title']); ?></span></td></tr>
                <tr>
                  <td>
                    <ul>
                      <li>
                        <span>Sent</span>
                        <h5><?php echo $email['total_delivered_emails']; ?></h5>
                        <?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($email['total_delivered_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							  <a href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list"></a>
							<?php }else{ ?>							  
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>
								<?php }else{?>
									<a class="view" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :   site_url("newsletter/emailreport/view/sent/".$key);?>"  >View</a>
									<a class="fancybox add_to_list" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/sent/".$key);?>" > </a>							  
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Opened</span>
                        <h5><?php echo $email['total_read_emails']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($email['total_read_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							  <a  href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list"></a>
							<?php }else{ ?>
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>
								<?php }else{?>
							  <a class="view" href="<?php echo site_url("newsletter/emailreport/view/read/".$key);?>"   >View</a>
							  <a class="fancybox add_to_list" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/read/".$key);?>"  ></a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Unopened</span>
                        <h5><?php echo $email['total_unread_emails']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($email['total_unread_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							  <a  href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list" ></a>
							<?php }else{ ?>
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>
								<?php }else{?>
							  <a class="view" href="<?php echo site_url("newsletter/emailreport/view/unread/".$key);?>" >View</a>
							  <a class="fancybox add_to_list" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/unread/".$key);?>"  ></a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Clicks</span>
                        <h5><?php echo $email['total_click_emails']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($email['total_click_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							  <a href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list" ></a>
							<?php }else{ ?>
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>		
								<?php }else{?>
							  <a class="view" href="<?php echo site_url("newsletter/emailreport/view/click/".$key);?>">View</a>
							  <a class="fancybox add_to_list" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/clickemail/".$key);?>" ></a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Forwards</span>
                        <h5><?php echo $email['email_track_forward']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($email['email_track_forward']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							<?php }else{ ?>
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>	
								<?php }else{?>
							  <a class="view" href="<?php echo site_url("newsletter/emailreport/view/forwardemail/".$key);?>">View</a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Unsubscribes</span>
                        <h5><?php echo $email['total_unsubscribes']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($email['total_unsubscribes']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							<?php }else{ ?>
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>	
								<?php }else{?>
									<a class="view" href="<?php echo site_url("newsletter/emailreport/view/unsubscribes/".$key);?>">View</a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Bounced</span>
                        <h5><?php echo $email['email_track_bounce']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($email['email_track_bounce']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							<?php }else{ ?>
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>	
								<?php }else{?>
									<a class="view" href="<?php echo site_url("newsletter/emailreport/view/bounced/".$key);?>" >View</a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Complaints</span>
                        <h5><?php echo $email['total_complaint_emails']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($email['total_complaint_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							<?php }else{ ?>
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>	
								<?php }else{?>
									<a class="view" href="<?php echo site_url("newsletter/emailreport/view/complaints/".$key);?>">View</a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                    </ul>
                  </td>
                </tr>
              </table>
            </td>
			<?php 
			if(!empty($email['is_ab'])){
				$refrenseCampignId = $email['is_ab']['refrense_campaign'];
				$refrenseCampign = $email['is_ab'][$refrenseCampignId];
				if(count($refrenseCampign) < 2 )
				{
					unset($refrenseCampign['campaign_title']);
					echo '<td width="50%"><table class="stats"><tr><td><strong>Note:</strong><span>Campaign Not Send Yet.</span></td></tr></table></td></tr><tr>';
				}else{
				
			?>
			<td width="50%" >
			  <table class="stats">
                <tr><td><strong>Sent at:</strong> <span><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($refrenseCampign['email_send_date'], $this->session->userdata('member_time_zone')) ))?></span></td></tr>
                <tr><td><strong>From:</strong> <span><?php echo ucfirst($refrenseCampign['sender_name'])." &lt;"; ?><?php echo $refrenseCampign['sender_email']."&gt;"; ?></span></td></tr>
                <tr><td><strong>List:</strong> <span><?php echo ucfirst($refrenseCampign['subscription_list_title']); ?></span></td></tr>
                <tr>
                  <td>
                    <ul>
                      <li>
                        <span>Sent</span>
                        <h5><?php echo $refrenseCampign['total_delivered_emails']; ?></h5>
                        <?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($refrenseCampign['total_delivered_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							  <a href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list"></a>
							<?php }else{ ?>							  
								<?php if($refrenseCampign['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>
								<?php }else{?>
									<a class="view" href="<?php echo ($refrenseCampign['total_delivered_emails']==0) ?  "javascript:void(0)" :   site_url("newsletter/emailreport/view/sent/".$refrenseCampignId);?>"  >View</a>
									<a class="fancybox add_to_list" href="<?php echo ($refrenseCampign['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/sent/".$refrenseCampignId);?>" > </a>							  
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Opened</span>
                        <h5><?php echo $refrenseCampign['total_read_emails']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($refrenseCampign['total_read_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							  <a  href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list"></a>
							<?php }else{ ?>
								<?php if($refrenseCampign['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>
								<?php }else{?>
							  <a class="view" href="<?php echo site_url("newsletter/emailreport/view/read/".$refrenseCampignId);?>"   >View</a>
							  <a class="fancybox add_to_list" href="<?php echo ($refrenseCampign['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/read/".$refrenseCampignId);?>"  ></a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Unopened</span>
                        <h5><?php echo $refrenseCampign['total_unread_emails']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($refrenseCampign['total_unread_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							  <a  href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list" ></a>
							<?php }else{ ?>
								<?php if($email['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>
								<?php }else{?>
							  <a class="view" href="<?php echo site_url("newsletter/emailreport/view/unread/".$refrenseCampignId);?>" >View</a>
							  <a class="fancybox add_to_list" href="<?php echo ($refrenseCampign['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/unread/".$refrenseCampignId);?>"  ></a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Clicks</span>
                        <h5><?php echo $refrenseCampign['total_click_emails']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($refrenseCampign['total_click_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							  <a href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list" ></a>
							<?php }else{ ?>
								<?php if($refrenseCampign['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>		
								<?php }else{?>
							  <a class="view" href="<?php echo site_url("newsletter/emailreport/view/click/".$refrenseCampignId);?>">View</a>
							  <a class="fancybox add_to_list" href="<?php echo ($refrenseCampign['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/clickemail/".$refrenseCampignId);?>" ></a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Forwards</span>
                        <h5><?php echo $refrenseCampign['email_track_forward']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($refrenseCampign['email_track_forward']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							<?php }else{ ?>
								<?php if($refrenseCampign['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>	
								<?php }else{?>
							  <a class="view" href="<?php echo site_url("newsletter/emailreport/view/forwardemail/".$refrenseCampignId);?>">View</a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Unsubscribes</span>
                        <h5><?php echo $refrenseCampign['total_unsubscribes']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($refrenseCampign['total_unsubscribes']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							<?php }else{ ?>
								<?php if($refrenseCampign['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>	
								<?php }else{?>
									<a class="view" href="<?php echo site_url("newsletter/emailreport/view/unsubscribes/".$refrenseCampignId);?>">View</a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Bounced</span>
                        <h5><?php echo $refrenseCampign['email_track_bounce']; ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($refrenseCampign['email_track_bounce']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							<?php }else{ ?>
								<?php if($refrenseCampign['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>	
								<?php }else{?>
									<a class="view" href="<?php echo site_url("newsletter/emailreport/view/bounced/".$refrenseCampignId);?>" >View</a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Complaints</span>
                        <h5><?php echo number_format($refrenseCampign['total_complaint_emails']); ?></h5>
						<?php if($this->session->userdata('manage_contacts')){ ?>
							<?php if($refrenseCampign['total_complaint_emails']==0){ ?>
							  <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
							<?php }else{ ?>
								<?php if($refrenseCampign['is_freezed']){?>
									<a class="view" href="javascript:void(0);" onclick="fancyAlert('Stats details are not available for email campaigns sent over 90 days ago.')">View</a>	
								<?php }else{?>
									<a class="view" href="<?php echo site_url("newsletter/emailreport/view/complaints/".$refrenseCampignId);?>">View</a>
								<?php } ?>
							<?php } ?>
                        <?php } ?>
                      </li>
                    </ul>
                  </td>
                </tr>
              </table>
            </td>
				</tr>
				<tr>
			<?php	} } ?>
            <td <?php if(!empty($email['is_ab'])) { echo 'width="50%" style="display:none" ';}else{ echo 'width="30%" '; } ?> >
              <div style="height: 295px; overflow: hidden;">
                <div id="piecontainer_<?php echo $key; ?>" style="height: 300px; overflow: hidden;"></div>
              </div>
            </td>
			
          </tr>
        </table>
      <?php } ?>
      <div class="pagination-container noajax"><?php echo $paging_links ?></div>
    <?php } else { ?>
      <div class="empty" style="height: 400px">
        <p style="padding-top: 100px">No campaigns have been created to track. Click “New Campaign” to get started.</p>
        <a href="<?php echo  site_url("newsletter/campaign/create");?>" class="btn add"><i class="icon-plus"></i>New Campaign</a>
      </div>
    <?php } ?>
  </div>
</div>
<!--[/body]-->
