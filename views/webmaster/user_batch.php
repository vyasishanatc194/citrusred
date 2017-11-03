<div id="messages" style="color:#FF0000;">
    <?php
// display all messages

    if (is_array($messages)):
        foreach ($messages as $type => $msgs):
            foreach ($msgs as $message):
                echo ('<span class="' . $type . '">' . $message . '</span>');
            endforeach;
        endforeach;
    endif;
    ?>
</div>
<span style="text-align: left;"><b>User Batch</span>

<table class="tbl_listing" width="100%">
    <thead>
        <tr>
            <th width="5%">&nbsp;</th>
            <th width="20%" >Batch Name</th>		
            <th width="20%" >Total Member</th>
            <th width="20%">Batch Grade</th>
			<th width="20%">Created Date</th>
            <th width="28%">More Actions</th>			 
        </tr>
    </thead>
    <?php
	
	
//List all users
    if (count($subscriber_data)) {
        $i = 1;
        foreach ($subscriber_data as $sd => $value) {
            ?>
            <tr>
                <td width="5%"><?php echo $i; ?></td>
                <td width="20%" ><?php echo $value['dv_csv_name']; ?></td>
                <td width="20%"><?php echo $value['dv_csv_count']; ?></td>
                <td width="20%"><?php echo $value['dv_batch_grade']; ?></td>
				<td width="20%"><?php echo date('Y-m-d',strtotime($value['dv_createddate'])); ?></td>
                <td width="28%">
                        <?php if($value['dv_scheduled'] == 0):?>
                        <a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/users_manage/user_cron_batch/'.$value['dv_id'].'/'.$value['rc_member_id']);?>" style="color:red;">
                            Schedule For Datavalidation
                        </a>
                    <?php else:?>
                        <a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" style="color:red;">
                            Scheduled
                        </a>
                    <?php endif;?>
					
					 <?php if($value['dv_singlecsv_run'] == 1):?>&nbsp;
                     <a style="border:1px solid #ccc;background:rgb(109,187,74); color:#fff;padding:2px 5px;" style="color:red;">
                            Completed
                        </a>
                    <?php endif;?>
					
                </td>

            </tr>



            <?php $i++;
        }
    } else { ?>
        <tr><td colspan="11" align="center">No Data Available</td></tr>
<?php } ?>
</table>