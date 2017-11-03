<script type="text/javascript">
    $(document).ready(function(){
    $(".fancybox").fancybox({'autoDimensions':false,'centerOnScroll':true,'scrolling':true,'width':'400','height':'auto'});
    });
  function fancyAlert(msg){
    $.fancybox({
    'content' : "<div style='width:400px;'><h5>Alert</h5><p style='text-align:center'>"+msg+"</p><div class='btn-group'><span class='btn cancel' onclick='$.fancybox.close();'>Close</span></div></div>"
    });
  }
</script>
  <!-- END: load jquery -->
 <?php if(count($emailreport_data)>0){ ?>
   <!-- BEGIN: load jquery -->
   <!--[if IE]><script language="javascript" type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/excanvas.js?v=6-20-13"></script><![endif]-->
  <!-- END: load jquery -->
 <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/jquery.jqplot.css?v=6-20-13" />
  <!-- END: load jquery -->

  <!-- BEGIN: load jqplot -->
<script language="javascript" type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.jqplot.js?v=6-20-13"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jqplot.pieRenderer.js?v=6-20-13"></script>
<script id="example_1" type="text/javascript">
		$(document).ready(function(){
		<?php foreach($emailreport_data as $key=>$email){	?>
		<?php if($email['total_delivered_emails']>0){ ?>
    s1 = [['Opened',<?php echo $email['per_read_emails']; ?>], ['Unopened', <?php echo $email['per_unread_emails']; ?>], ['Bounced', <?php echo $email['per_bounce_emails']; ?>]];
    plot1 = $.jqplot('piecontainer_<?php echo $key; ?>', [s1], {
        grid: {
            drawBorder: false,
            drawGridlines: false,
            background: '#ffffff',
            shadow:false
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
<?php } }?>
});
</script>
<?php } ?>
  <div id="body-dashborad" class="nobackground">
    <div class="container">
      <h1>Stats</h1>
      <?php if(count($emailreport_data)>0){ ?>
      <?php foreach($emailreport_data as $key=>$email){ ?>
        <table class="report">
          <tr><td colspan="2"><h2><?php echo $email['campaign_title']; ?></h2></td></tr>
          <tr valign="top">
            <td width="70%" >
              <table class="stats">                
                <tr><td><strong>From:</strong> <span><?php echo ucfirst($email['sender_name'])." &lt;"; ?><?php echo $email['sender_email']."&gt;"; ?></span></td></tr>
                
                <tr>
                  <td>
                    <ul>
                      <li>
                        <span>Sent</span>
                        <h5><?php echo $email['total_delivered_emails']; ?></h5>
                        <?php if($email['total_delivered_emails']==0){ ?>
                          <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
                          <a href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list"></a>
                        <?php }else{ ?>
                          <a class="view" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :     site_url("newsletter/emailreport_autoresponder/view/sent/".$key."/".$scheduled_id);?>"  >View</a>
                          <a class="fancybox add_to_list" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/sent/".$key);?>" > </a>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Opened</span>
                        <h5><?php echo $email['total_read_emails']; ?></h5>
                        <?php if($email['total_read_emails']==0){ ?>
                          <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
                          <a  href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list"></a>
                        <?php }else{ ?>
                          <a class="view" href="<?php echo site_url("newsletter/emailreport_autoresponder/view/read/".$key."/".$scheduled_id);?>"   >View</a>
                          <a class="fancybox add_to_list" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/read/".$key);?>"  ></a>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Unopened</span>
                        <h5><?php echo $email['total_unread_emails']; ?></h5>
                        <?php if($email['total_unread_emails']==0){ ?>
                          <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
                          <a  href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list" ></a>
                        <?php }else{ ?>
                          <a class="view" href="<?php echo site_url("newsletter/emailreport_autoresponder/view/unread/".$key."/".$scheduled_id);?>" >View</a>
                          <a class="fancybox add_to_list" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/unread/".$key);?>"  ></a>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Clicks</span>
                        <h5><?php echo $email['total_click_emails']; ?></h5>
                        <?php if($email['total_click_emails']==0){ ?>
                          <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
                          <a href="javascript:void(0);" onclick="fancyAlert('No Records!')" class="add_to_list" ></a>
                        <?php }else{ ?>
                          <a class="view" href="<?php echo site_url("newsletter/emailreport_autoresponder/view/click/".$key."/".$scheduled_id);?>">View</a>
                          <a class="fancybox add_to_list" href="<?php echo ($email['total_delivered_emails']==0) ?  "javascript:void(0)" :site_url("newsletter/contacts/add_emailreport_to_contact_list/clickemail/".$key);?>" ></a>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Forwards</span>
                        <h5><?php echo $email['email_track_forward']; ?></h5>
                        <?php if($email['email_track_forward']==0){ ?>
                          <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
                        <?php }else{ ?>
                          <a class="view" href="<?php echo site_url("newsletter/emailreport_autoresponder/view/forwardemail/".$key."/".$scheduled_id);?>">View</a>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Unsubscribes</span>
                        <h5><?php echo $email['total_unsubscribes']; ?></h5>
                        <?php if($email['total_unsubscribes']==0){ ?>
                          <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
                        <?php }else{ ?>
                          <a class="view" href="<?php echo site_url("newsletter/emailreport_autoresponder/view/unsubscribes/".$key."/".$scheduled_id);?>">View</a>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Bounced</span>
                        <h5><?php echo $email['email_track_bounce']; ?></h5>
                        <?php if($email['email_track_bounce']==0){ ?>
                          <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
                        <?php }else{ ?>
                          <a class="view" href="<?php echo site_url("newsletter/emailreport_autoresponder/view/bounced/".$key."/".$scheduled_id);?>" >View</a>
                        <?php } ?>
                      </li>
                      <li>
                        <span>Complaints</span>
                        <h5><?php echo $email['total_complaint_emails']; ?></h5>
                        <?php if($email['total_complaint_emails']==0){ ?>
                          <a class="view" href="javascript:void(0);" onclick="fancyAlert('No Records!')">View</a>
                        <?php }else{ ?>
                          <a class="view" href="<?php echo site_url("newsletter/emailreport_autoresponder/view/complaints/{$key}/{$scheduled_id}/");?>">View</a>
                        <?php } ?>
                      </li>
                    </ul>
                  </td>
                </tr>
              </table>
            </td>
            <td width="30%">
              <div style="height: 295px; overflow: hidden;">
                <div id="piecontainer_<?php echo $key; ?>" style="height: 300px; overflow: hidden;"></div>
              </div>
            </td>
          </tr>
        </table>
      <?php } ?>
      <div class="pagination-container noajax"><?php echo $paging_links ?></div>
    <?php } else { ?>
      <div class="empty">
        <p>Nothing to track. No campaigns have been sent.</p>
      </div>
    <?php } ?>
  </div>
</div>
<!--[/body]-->
