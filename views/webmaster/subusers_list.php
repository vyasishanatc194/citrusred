<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css" />
<script type="text/javascript">
	$(document).ready(function(){
		$(".fancybox").fancybox({'centerOnScroll':true,'height':'200','width':'300','scrolling':'auto'});
    });
 
 
</script>
<?php if($mode!="paid_users"){?>
<script type="text/javascript">
	jQuery('.tblheading').find('a').live('click',function(){
		//alert(jQuery(this).attr('href'));
		$("#Src_frm").attr("action", jQuery(this).attr('href'));
		$('#Src_frm').submit();
		return false;
	});
	
</script>
 
<?php } ?>
<div id="messages" style="color:#FF0000;">
<?php
// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;

?>
</div>
 <span style="text-align: left;"><b>Total Sub-Users:</b> <?php echo $totusercount;?></span>
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/users_manage/subuser_create"><strong>Create Sub-User</strong></a></div>
<div class="tblheading">Manage Sub-Users <?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="50%">
	<thead>
		<tr>
			<th width="5%">ID</th>
			<th width="10%">User</th>			 		
			<th width="30%">Actions</th>								 
		</tr>
	</thead>
	<?php
//List all users
if(count($users)) {
foreach($users as $user){
?>
<tr>
	<td><?php echo $user['member_id'];  ?></td>
	<td>
		<label style="color:<?php echo ($user['affiliate_status'])?'#ff6500':'#000000';?>;font-weight:bold;"><?php echo $user['member_username'];  ?><?php echo ($user['affiliate_status'])?' *':'';?></label>
		<br/>
		<?php echo $user['email_address'];  ?>
		<br/>Created On: <?php echo date('d-M-Y', strtotime( $user['created_on']));  ?>
	</td>	  
	<td>
	<ul style="list-style:disc;line-height:20px;margin:0 0 0 10px;padding:0;">		
		<li style="float:left;width:100px;"><a href="<?php echo  site_url('webmaster/users_manage/subuser_create/'.$user['member_id']);?>" style="color:red;">Edit</a></li>
		<li style="float:left;width:100px;"><a onclick="return confirm('Are you sure to delete user')" href="<?php echo  site_url('webmaster/users_manage/user_delete/'.$user['member_id']);?>" style="color:red;">Delete</a></li>
	</ul>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="11" align="center">No Sub-User Available</td></tr>
<?php } ?>
</table>
