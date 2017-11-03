<script language="javascript" type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/datetimepick/datetimepicker.js?v=6-20-13"></script>
<!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <div class="dashboard-home">

        <h1>Send Campaign</h1>
        <?php
		if(validation_errors()){
			echo '<div style="color:#FF0000;" class="info">'.validation_errors().'</div>';
		}
		?>
		 <?php
				// display all messages

				if (is_array($messages)):
					echo '<div class="info">';
					foreach ($messages as $type => $msgs):
						foreach ($msgs as $message):
							echo ('<span class="' .  $type .'">' . $message . '</span>');
						endforeach;
					endforeach;
					echo '</div>';
				endif;

				?>

       <?php
			echo form_open('newsletter/campaign/send', array('id' => 'form_campaign_send','name'=>'form_campaign_send','class'=>"form-website"));
	 ?>

       <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="32%" class="label">Select Campaign</td>
    <td width="68%">
	<?php echo form_dropdown('campaigns',$campaign_data['campaigns'],$_REQUEST['campaigns']); ?>
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

echo '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';

foreach($subscription_data['subscriptions'] as $subscription){



	if(is_array($_REQUEST['subscriptions']) && in_array($subscription['subscription_id'],$_REQUEST['subscriptions']))

		$checked=true;

	else

		$checked=false;

	echo '<td>';

	echo form_checkbox(array('name'=>'subscriptions[]','id'=>'subscriptions','value'=>$subscription['subscription_id'],'checked'=>$checked ,'style'=>'width:10px'))."</td><td>";

	echo  $subscription['subscription_title'];

	echo '</td>';



	$i++;

	if($i%4==0) echo '</tr><tr>';

}



echo '</tr></table>';
 ?>
  </td>
  </tr>
  <tr>
    <td class="label">Scheduled Date</td>
    <td>
	<?php echo '<input value="'.$_REQUEST['scheduled_date'].'" id="scheduled_date" name="scheduled_date" type="text" size="40">

		<a href="javascript:NewCal(\'scheduled_date\',\'ddmmyyyy\',true,24)"><img src="'. base_url().'webappassets/js/datetimepick/images/cal.gif?v=6-20-13" width="16" height="16" border="0" alt="Pick a date"></a>';
	?>
	</td>
  </tr>
  <tr>
    <td class="label">&nbsp;</td>
    <td>
	<?php
		echo form_submit(array('name' => 'campaign_submit', 'id' => 'btnEdit','class'=>'button-input','content' => 'Send Campaign'), 'Send Campaign');
		echo '&nbsp;';
		echo form_button(array('name'=>'campaign_cancel','class'=>'buttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/campaign'."'"));
	?>
	</td>
  </tr>
</table>
    <?php
		echo form_hidden('action','send');

		echo form_close();
	?>


      </div>
    </div>
  </div>
  <!--[/body]-->
