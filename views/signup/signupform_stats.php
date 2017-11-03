<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.fastconfirm.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/jquery.fastconfirm.css?v=6-20-13" media="screen" />

<script type="text/javascript">
    $(document).ready(function(){
      $(".fancybox").fancybox({'autoDimensions':false,'centerOnScroll':true,'scrolling':true,'width':'400','height':'230'});
    });
    function confirmExport(act, cid, url){
      $(".export_csv").fastConfirm({
        position: "top",
        questionText: "Are you sure you want to export this list?",
        onProceed: function(trigger) {
          window.location = "<?php echo base_url();?>newsletter/emailreport/exportcsv/"+ act +'/'+cid+'/'+url+'/';
        },
        onCancel: function(trigger) {
        }
      });
    }
</script>
<div id="body-dashborad" class="nobackground">
  <div class="container">
    <h1>Form Stats</h1>
    <div class="left-menu contacts stats">      
      <div class="editing-theme-box <?php echo $current_tab == 'signups' ? 'active' : '';?>" >
        <div class="listname-no">
          <strong class="subscription_strong">Signups</strong>
          <span class="right-no"><?php echo $signup_count;?></span>
        </div>
      </div>
  
      <div class="backdrop"></div>
    </div>
    <div class="right-menu contacts stats">
      
      <div class="emailreport_list">
        <h2 class="list_title">
          <a href="<?php echo CAMPAIGN_DOMAIN.'s/'.$signupform_id;?>" target="_blank" onMouseOver="this.style.textDecoration='underline'"  onMouseOut="this.style.textDecoration='none'"><?php echo trim($form_detail['form_name']) ?></a>
         
          <a class="fancybox add_to_list add btn" href="<?php echo site_url("newsletter/contacts/add_signupform_contact_to_list/confirmation/$signupform_id");?>">
            <i class="icon-plus"></i>Add to List
          </a>
		          
        </h2>

        <strong>Created on:</strong>
        <span><?php 		
		echo date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($form_detail['date_added'], $this->session->userdata('member_time_zone')))); 
		?></span>
        <?php echo form_open('refer_friend/index', array('id' => 'frmReferFriend')); ?>
        <table class="list tbl-contacts" id="results" width="100%">
          <tr>
            <th width="50%;"><strong>Email Address</strong></th>
            <th width="50%"><strong>Name</strong></th>            
          </tr>
          <?php
           if(isset($emailreport_data)){
              if(count($emailreport_data)>0){
                foreach($emailreport_data as $subscriber){ ?>
                <tr>
                  <td>
                    
                      <a class="view_subscriber" href="<?php echo site_url('newsletter/subscriber/view/'.$subscriber['subscriber_id']); ?>"><?php echo $subscriber['subscriber_email_address']; ?></a>
                    
                  </td>
                  <td>
                    <?php echo $subscriber['subscriber_first_name']." ".$subscriber['subscriber_last_name']; ?>
                  </td>
				  
                </tr>
                <?php }?>

				 <tr class="contacts_change">
                              <th colspan="2" class="export-container">

								 <a href="javascript:void(0);" onclick="javascript:confirmExport('signup',<?php echo $signupform_id;?>);" class="export_csv btn cancel">
                                 <img src="<?php echo $this->config->item('webappassets');?>/images/table-export.png?v=6-20-13" alt="" align="absmiddle"> Export
                                </a>
                              </th>
                            </tr>
			<?php
			}else{ ?>
                  <tr><td colspan="2">No record found</td></tr>
            <?php }
			}
			?>
           
        </table>
        <div class="pagination_div noajax pagination-container">
        <?php echo $paging_links; ?>
      </div>
    </form>

      </div>
    </div>
  </div>

