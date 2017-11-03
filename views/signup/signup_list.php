<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.fastconfirm.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/jquery.fastconfirm.css?v=6-20-13" media="screen" />
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.blockUI.js?v=6-20-13"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
<?php echo link_tag('webappassets/css/signup_form.css?v=6-20-13'); ?>
<script type="text/javascript">
	$(document).ready(function(){
		$(".fancybox").fancybox();
	});
  $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
</script>
<script type="text/javascript">

  jQuery(".delete-list").live('click',function(event)
  {
    jQuery(this).fastConfirm({
      position: "top",
      questionText: "Are you sure you want to delete this Signup Form?",
      onProceed: function(trigger) {
        var signup_form_id=jQuery(trigger).attr('name');
         jQuery.ajax({
          url: "<?php echo base_url(); ?>newsletter/signup/signup_delete/"+signup_form_id,
          type:"POST",
          success: function(data){
            jQuery(trigger).parents('#signup_form_'+signup_form_id).remove();
            if($('.editing-theme-box').length<=0){
              $('.create_signup_form').show();
              $('.copy_code').hide();
              $('.view_code_container').remove();
            }
          }
        });
      },
      onCancel: function(trigger) {
      }
    });

  });
  function get_share(signup_id, link){
    $("#copy_link").val(link);

    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/signup/subscribe/"+signup_id+"/code",
      type:"POST",
      success: function(data) {
        $('.editing-theme-box').removeClass("active");
        $('#signup_form_'+signup_id).addClass("active");
        $('.copy_code').show();
        $('.view_code_container').hide();
        $('#copy_code').html(data);
      }
    });
  }
  function goto_page(signup_id){
      window.location.href="<?php echo base_url(); ?>newsletter/signup/signup_edit/"+signup_id;
  }

  function view_form(signup_id){
    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/signup/subscribe/"+signup_id+"/view_code",
      type:"POST",
      success: function(data) {
        var obj=jQuery.parseJSON(data);
        $('.editing-theme-box').removeClass("active");
        $('#signup_form_'+signup_id).addClass("active");
        $('.copy_code').hide();
        $('.view_code_container').show();
        $('.view_overview').html(obj.view_overview);
        $('.view_code').html(obj.view_code);
        $('.view_code').css("background-color",obj.background_color);
        $('.view_code').css("background-image",obj.background_image);
        $('.view_code').css("background-repeat",obj.background_repeat);
      }
    });
  }
  function enable_stats(signup_id){
    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/signup/enable_stats/"+signup_id,
      type:"POST",
      success: function(data) {
        view_form(signup_id);
      }
    });
  }
</script>


  <!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <h1>
        <a href="<?php echo  site_url("newsletter/signup/signup_create");?>" class="btn add"><i class="icon-plus"></i>Create Signup Form</a>
        Signup Forms
      </h1>

      <div class="left-menu contacts">
        <?php
          //Fetch signup_froms from signup_froms array
          $i=1;
        ?>
        <?php foreach($signup_froms['forms'] as $signup_from){ ?>
          <div class="editing-theme-box <?php if($i==1){?> active<?php } ?>" id="signup_form_<?php echo $signup_from['id']; ?>">
            <div class="listname-no">
              <strong class="subscription_strong" onclick="view_form(<?php echo $signup_from['id']; ?>)">
                <?php echo $signup_from['form_name']; ?>
              </strong>
              <span class="right-no">&nbsp;</span>
            </div>
            <div class="icon-listing">
              <ul class="list-icons contacts">
                <li><a href="javascript:void(0);" onclick="goto_page(<?php echo $signup_from['id']; ?>)" class="btn cancel delete_contact">
				<img class="campion_send" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Email-Icon-Edit.png?v=6-20-13" alt="campaigns"/>
				</a></li>
                <li>
				
				
				<a href="javascript:void(0);" class="link btn cancel delete_contact" onclick="get_share(<?php echo $signup_from['id']; ?>,'<?php echo CAMPAIGN_DOMAIN.'s/'.$signup_from['id'] ?>')"><img class="campion_send" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Export-Icon.png?v=6-20-13" alt="campaigns"/></a></li>
                <li><a href="javascript:void(0);" class="delete-list btn cancel delete_contact" name="<?php echo $signup_from['id']; ?>"><img class="campion_send" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Email-Icon-Trash.png?v=6-20-13" alt="campaigns"/></a></li>
              </ul>
            </div>
          </div>
         <?php
          $i++;
          } ?>
        <div style="padding-bottom: 1px"></div>
        <div class="backdrop"></div>
      </div>
      <div class="right-menu contacts forms">
        <?php if(count($signup_froms['forms'])) { ?>
          <div class="view_code_bg">
            <div class="copy_code">
              <h2>Share</h2>
              <strong>Quick link to your Signup Form</strong>
              <span>(Copy &amp; paste in an email, on your website or blog, Facebook, Twitter or any other social network.)</span>
              <input id="copy_link" type="text" onclick="this.setSelectionRange(0, this.value.length)" class="clean" />
              <strong>Copy and Paste Code</strong>
              <textarea id="copy_code" onclick="this.setSelectionRange(0, this.value.length)"></textarea>
            </div>
          </div>
          <div class="view_code_container">
            <h2>Overview</h2>
			<!-- Overview via AJAX -->
			<div class="view_overview"></div>		 
            <div class="splitter"></div>
            <h2>Sign Up Form Preview</h2>
			<!-- Signup form via AJAX -->
            <div class="view_code"></div>
          </div>
        <?php } else { ?>
          <div class="empty sign-up-form-descp" style="height: 400px">
            <p style="padding-top: 100px">To begin collecting new contacts from your website, click on “Create Signup Form”.</p>
            <a href="<?php echo  site_url("newsletter/signup/signup_create");?>" class="btn add"><i class="icon-plus"></i>Create Signup Form</a>
          </div>
        <?php  } ?>
      </div>
    </div>
  </div>
      <!--[/navigation]-->

  <!--[/body]-->
<?php if(count($signup_froms['forms'])>0) { ?>
<script type="text/javascript">
view_form(<?php echo $signup_froms['forms'][0]['id']; ?>)
</script>
<?php } ?>
