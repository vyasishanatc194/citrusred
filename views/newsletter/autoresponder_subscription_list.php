<script language="javascript" type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/datetimepick/datetimepicker.js?v=6-20-13"></script>
<!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <div class="dashboard-home">

        <h1>Select Subscriber</h1>
        <?php
		if(validation_errors()){
			echo '<div style="color:#FF0000;" class="info">'.validation_errors().'</div>';
		}
		?>
		 <?php
				// display all messages

				if (is_array($messages)):
					echo '<div class="info" style="background:none;background-image:none;border:none;background-color:none;">';
					foreach ($messages as $type => $msgs):
						foreach ($msgs as $message):
							echo ('<span class="' .  $type .'">' . $message . '</span>');
						endforeach;
					endforeach;
					echo '</div>';
				endif;

				?>

       <?php
			echo form_open('newsletter/autoresponder/email_setting/'.$autoresponder_data['autoresponders']['autoresponder_id'], array('id' => 'form_autoresponder_send','name'=>'form_autoresponder_send','class'=>"form-website"));
	 ?>

   <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="32%" class="label">Autoresponder</td>
    <td width="68%">
		<b><?php echo ucwords($autoresponder_data['autoresponders']['autoresponder_title']); ?></b>
	</td>
  </tr>
  <tr>
    <td height="15" ></td>
    <td></td>
  </tr>
  <tr>
    <td height="25" class="label">Subscriptions</td>
    <td>
 <?php $i=0;

echo '<div style="height:300px;width:300px; padding:10px;border:1px solid #A4A4A4; overflow-y:scroll;background:none repeat scroll 0 0 #FBF8F2;"><ul>';

foreach($subscription_data['subscriptions'] as $subscription){



	if(isset($_REQUEST['subscriptions']) && in_array($subscription['subscription_id'],$_REQUEST['subscriptions']))

		$checked=true;

	else

		$checked=false;

	echo '<li style="padding:3px 3px;">';

	echo form_checkbox(array('name'=>'subscriptions[]','id'=>'subscriptions','value'=>$subscription['subscription_id'],'checked'=>$checked ,'style'=>'display:inline;margin-right:15px;')).ucwords($subscription['subscription_title']);


	echo '</li>';



	$i++;

}
if($i<=0){
	echo "Please Create Subscriptions";
}


echo '</ul></div>';
 ?>
  </td>
  </tr>

  <tr>
    <td class="label">&nbsp;</td>
    <td>
	<?php
		echo form_submit(array('name' => 'campaign_submit', 'id' => 'btnEdit','class'=>'button-input','content' => 'Next'), 'Next');
		echo '&nbsp;';
		echo form_button(array('name'=>'autoresponder_cancel','class'=>'buttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/autoresponder/display'."'"));
	?>
	</td>
  </tr>
</table>
    <?php
		echo form_hidden('action','subscription_list');

		echo form_close();
	?>


      </div>
    </div>
  </div>
  <!--[/body]-->
