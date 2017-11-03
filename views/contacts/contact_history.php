
				<?php foreach($email_report as $activity){

				$emailSubject = ($activity['email_subject']!='')?$activity['email_subject']:$activity['campaign_title'];

					if($activity['email_track_bounce']>0){
				?>
				<tr>
					<td>
					<?php if(is_null($activity['bounce_date'])){ ?>
					<a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($activity['email_send_date'], $this->session->userdata('member_time_zone')))); ?></a>
					<?php }else{ ?>
					<a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($activity['bounce_date'],$this->session->userdata('member_time_zone')))); ?></a>
					<?php } ?>
					</td>
					<td><a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$activity['campaign_id'];?>"   title="view" target="_blank"><?php echo $emailSubject ?></a></td>
						<?php if($activity['email_track_bounce']==2) { ?>
							<td>Soft Bounce</td>
						<?php }  else if($subscriptions[0]['subscrber_bounce']!=1) { ?>
							<td>Hard Bounce</td>
						<?php }else if($activity['soft_bounce_status']>$max_soft_bounce){ ?>
							<td>Hard Bounce</td>
						<?php }else{ ?>
							<td>Soft Bounce</td>
						<?php } ?>
				</tr>
				<?php } else if($activity['email_track_complaint']>0){ ?>
				<tr>
					<td>
					<?php if(is_null($activity['complaint_date'])){ ?>
					<a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($activity['email_send_date'], $this->session->userdata('member_time_zone')))); ?></a>
					<?php }else{ ?>
					<a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($activity['complaint_date'], $this->session->userdata('member_time_zone')))); ?></a>
					<?php } ?>
					</td>
					<td><a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$activity['campaign_id'];?>"   title="view" target="_blank"><?php echo $emailSubject; ?></a></td>
					<td>Complaint</td>
				</tr>
				<?php }else if(($activity['email_track_read']<=0)&&($activity['email_sent']>0)){ ?>
				<tr>
					<td>
						<?php if($activity['email_send_date']!== NULL){ ?>
						<a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($activity['email_send_date'], $this->session->userdata('member_time_zone')))); ?></a>
						<?php }else{ ?>
						<a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($activity['email_receive_date'], $this->session->userdata('member_time_zone')) )); ?></a>
						<?php } ?>
					</td>
					<td><a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$activity['campaign_id'];?>"   title="view" target="_blank"><?php echo $emailSubject; ?></a></td>
					<td>Sent</td>
				</tr>
				<?php }else{
					 $date_array=array(
						'date_unsubscribe'=>$activity['date_unsubscribe'],
						'date_forward'=>$activity['date_forward'],
						'date_click'=>$activity['date_click'],
					);
					arsort($date_array);
				?>
				<?php foreach($date_array as $key=>$value){?>
					<?php if($key=="date_unsubscribe"){?>
						<?php if($activity['email_track_unsubscribes']>0){?>
							<tr>
								<?php if(is_null($activity['date_unsubscribe'])){
										$read_date=$activity['email_send_date'];
									}else{
										$read_date=$activity['date_unsubscribe'];
									}
								?>
								<td><a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($read_date, $this->session->userdata('member_time_zone')) ) ); ?></a></td>
								<td><a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$activity['campaign_id'];?>"   title="view" target="_blank"><?php echo $emailSubject; ?></a></td>
								<td>Unsubscribed</td>
							</tr>
						<?php } ?>
					<?php }else if($key=="date_forward"){ ?>
						<?php if($activity['email_track_forward']>0){?>
							<tr>
								<?php if(is_null($activity['date_forward'])){
										$read_date=$activity['email_send_date'];
									}else{
										$read_date=$activity['date_forward'];
									}
								?>
								<td><a href="<?php echo site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($read_date, $this->session->userdata('member_time_zone')) ) ); ?></a></td>
								<td><a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$activity['campaign_id'];?>"  title="view" target="_blank"><?php echo $emailSubject; ?></a></td>
								<td>Forward to friend(<?php echo $activity['email_track_forward']; ?>)</td>
							</tr>
						<?php } ?>
					<?php }else if($key=="date_click"){ ?>
						<?php
				if(count($email_report_click[$activity['campaign_id']])>0){ ?>
				<tr>
					<?php if(is_null($activity['date_click'])){
							$read_date=$activity['email_send_date'];
						}else{
							$read_date=$activity['date_click'];
						}
					?>
					<td><a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($read_date, $this->session->userdata('member_time_zone')) ) ); ?><a/></td>
					<td><a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$activity['campaign_id'];?>"   title="view" target="_blank"><?php echo $emailSubject; ?></a></td>
					<td>
					<div  class="history_click">
						<div id="button">
							<a class="contact_frm" onclick="javascript:$(this).parent().parent().parent().parent().next('.click_list').slideToggle();" href="javascript:void(0);">
								<span>
									Clicks (<?php echo $activity['clicks']; ?>)<img class="dropsub_img" src="<?php echo $this->config->item('webappassets');?>images/tab_arrow.png?v=6-20-13"  alt="" align="absmiddle" >
								</span>
							</a>
						</div>
					</div>
					</td>
				</tr>
						<?php
							if(count($email_report_click[$activity['campaign_id']])>0){
							$i=0;
						?>
							<tr class="click_list" style="display:none;"><td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tbl-small" >
							<?php
								foreach($email_report_click[$activity['campaign_id']] as $click){
									if($i%2==0){
										$style="style='background-color:#CCCCCC'";
									}else{
										$style="style='background-color:#D3D6D7;border-color:#D3D6D7;'";
									}
							?>
									<tr <?php echo $style; ?>><td <?php echo $style; ?> width="75%"><a href="<?php echo $click['actual_url']; ?>" target="_blank"><?php echo substr($click['actual_url'],0,70); ?></a></td><td <?php echo $style; ?>><?php echo $click['cnt']; ?> Clicks</td></tr>
							<?php
							$i++;
							}
							?>
							</table></td></tr>
						<?php }?>
						<?php } ?>
					<?php } ?>
				<?php } ?>
					<?php if($activity['email_track_read']>0){?>
						<tr>
							<?php if(is_null($activity['email_track_read_date'])){
									$read_date=$activity['email_send_date'];
								}else{
									$read_date=$activity['email_track_read_date'];
								}
							?>
							<td><a href="<?php echo  site_url("newsletter/emailreport/display/".$activity['campaign_id']);?>"><?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($read_date, $this->session->userdata('member_time_zone')) ) ); ?></a></td>
							<td><a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$activity['campaign_id'];?>" title="view" target="_blank"><?php echo $emailSubject; ?></a></td>
							<td>Opened</td>
						</tr>
				<?php } ?>
				<?php } ?>
				<?php } ?>

