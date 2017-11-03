<title>Feedback - Email Marketing</title>
<script type="text/javascript">
	function submit_form(){
		var block_data="";
		block_data+=$('#feedback_frm_submit').serialize()+'&submit_action=submit';
		//$.fancybox.close();
		jQuery.ajax({
			url: "<?php echo base_url() ?>feedback/create",
			type:"POST",
			data:block_data,
			success: function(data){
				var data_arr=data.split(":", 2);
				if(data_arr[0]=="error"){
					$('.msg').show();
					$('.msg').html(data_arr[1]);
					setTimeout( function(){$('.msg').hide();$.fancybox.close();} , 4000);
					//$.fancybox.close();
				}else{
					$('.msg').show();
					$('.msg').html(data_arr[1]);
					setTimeout( function(){$('.msg').hide();$.fancybox.close();} , 1000);
				}
				
			}
		});
	}
</script>
<div class="fancybox-page registration-page_contact_delete">
	<h5>Feedback</h5>
		<form  method="post" name="feedback_frm_submit" id="feedback_frm_submit" onsubmit="submit_form(); return false;">
			<div id="feedback-form">
				<div class="msg info" style="display: none"></div>
				<strong>Your Email Address</strong>
				<input type="text" name="email_address" id="email_address" />
				<strong>Subject</strong>
				<select name="subject" id="subject">
					<option value="General Question">General Question</option>
					<option value="Report a Bug">Report a Bug</option>
				</select>
				<p>
					What would you like us to know? Please fill in details below, and we'll get back to you as soon as possible.
				</p>
				<textarea name="message" id="message"></textarea>
			</div>

			<div class="btn-group">
				<?php echo form_submit(array('name' => 'feedback_submit', 'id' => 'btnEdit','class'=>'btn add add_more','content' => 'Submit','style' => 'height:auto'), 'Submit'); ?>
				<?php echo form_button(array('name' => 'feedback_cancel', 'id' => 'feedback_cancel','class'=>'btn cancel','content' => 'Cancel','onclick'=>'javascript:$.fancybox.close();'), 'Cancel'); ?>
			</div>
		</form>
	</div>
</div>
