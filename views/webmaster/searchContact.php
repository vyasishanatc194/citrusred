<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" media="screen" />
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.blockUI.js?v=6-20-13"></script>
<script type="text/javascript">
    
	function srchnow(){
		var c = $('#srchContact').val();
		
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/srchcontactnow/",
		  type:"POST",
		  data: "e="+c,
		  success: function(data) {
			$('.search_contacts').html(data);
		  }
		});	
	}
	function unsubIt(cid){
		var c = $('#srchContact').val();
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/unsubit/",
		  type:"POST",
		  data: "e="+c+"&cid="+cid,
		  success: function(data) {
			$('.search_contacts').html(data);
		  }
		});	
	}
    function unsuball(){
		var c = $('#srchContact').val();
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/unsuball/",
		  type:"POST",
		  data: "e="+c,
		  success: function(data) {
			$('.search_contacts').html(data);
		  }
		});	
	}
  $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
</script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets'); ?>js/jquery.fastconfirm.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets'); ?>css/jquery.fastconfirm.css?v=6-20-13" media="screen" />
<script type="text/javascript" src="<?php echo $this->config->item('webappassets'); ?>js/ajaxfileupload.js?v=6-20-13"></script>
<script type="text/javascript">
var base_url="<?php echo base_url();?>";
var webappassets="<?php echo $this->config->item('webappassets');?>";
var memid="<?php echo $extra['member_id'];?>";
</script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets'); ?>js/contacts_add_admin.js?v=6-20-13"></script>
<!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      
      <div class="form-fields">
        <div class="import_contact subscriber_menus">
          <div class="subscriber_msg_remove" style="display:block;"></div>
          <?php echo form_open_multipart('webmaster/contacts_segmentation/addDNMSubmit/', array('id' => 'form_dnm_import','name'=>'form_dnm_import','class'=>"form-website")); ?>
            <div>
			<b>Search Contact:</b> <input name='srchContact' id='srchContact' style="width:300px;"/>			<input value="Search" type="button" name="btnSrchContact" id="btnSrchContact" onclick="javascript:srchnow();" />
			</div>
            <div class="search_contacts"></div>
          </form>
        </div>
        
      </div>
    </div>
  </div>
