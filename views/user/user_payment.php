 <!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <div class="dashboard-home">
        <h1>Payment Information</h1>
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
			
			echo form_open('user/packages/', array('id' => 'frmPackages','name' => 'frmPackages','class'=>'form-website'));
		?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="32%" class="label">Credit Card Number</td>
              <td width="68%">
			  <?php 
			  
				echo form_input(array('name'=>'cc_number','id'=>'cc_number','maxlength'=>120,'size'=>40,'value'=>set_value('cc_number') ));
				
				?>
			</td>
            </tr>
            
            <tr>
              <td class="label">Credit Card Expiration Month</td>
              <td>
			  <?php 
				 $months_array=array(1=>'January',2=>'Febuary',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
//echo form_input(array('name'=>'ccexp_month','id'=>'ccexp_month','maxlength'=>12,'size'=>10,'value'=>set_value('ccexp_month')));
				echo form_dropdown('ccexp_month',$months_array,$_REQUEST['ccexp_month']);
			?>

			           </td>
            </tr>
            <tr>
              <td class="label">Credit Card Expiration Year</td>
              <td>
			 <?php
				echo form_input(array('name'=>'ccexp_year','id'=>'ccexp_year','maxlength'=>12,'size'=>40,'value'=>set_value('ccexp_year')));
				?>
			  </td>
            </tr>
            <tr>
              <td class="label">Credit Card CVV Number</td>
              <td>
				<?php
					echo form_input(array('name'=>'cvv','id'=>'cvv','maxlength'=>12,'size'=>10,'value'=>set_value('cvv') ));
				?>
			</td>
            </tr>
			<tr>
				<td colspan='2'>
					<h1>Billing Information</h1>
				</td>
			</tr>
            <tr>
              <td class="label">First Name</td>
              <td>
				<?php 
				echo form_input(array('name'=>'first_name','id'=>'first_name','maxlength'=>120,'size'=>40,'value'=>$user_data['first_name'] ));
				?>
			  </td>
            </tr>
            <tr>
              <td class="label">Last Name</td>
              <td>
			  <?php
				echo form_input(array('name'=>'last_name','id'=>'last_name','maxlength'=>120,'size'=>40,'value'=>$user_data['last_name']));
				?>
			  </td>
            </tr> 
			<tr>
              <td class="label">Email</td>
              <td>
			  <?php
				echo form_input(array('name'=>'email','id'=>'email','maxlength'=>150,'size'=>40,'value'=>$user_data['email_address'] ));
				?>
			  </td>
            </tr>
			<tr>
              <td class="label">Phone</td>
              <td>
			  <?php
				echo form_input(array('name'=>'phone','id'=>'phone','maxlength'=>50,'size'=>40,'value'=>$user_data['phone_number'] ));
				?>
			  </td>
            </tr>
			<tr>
              <td class="label">Address Line1</td>
              <td>
			  <?php
				echo form_input(array('name'=>'address1','id'=>'address1','maxlength'=>60,'size'=>40,'value'=>$user_data['address_line_1'] ));
				?>
			  </td>
            </tr>
			<tr>
              <td class="label">City</td>
              <td>
			  <?php
				echo form_input(array('name'=>'city','id'=>'city','maxlength'=>50,'size'=>40,'value'=>$user_data['city'] ));
				?>
			  </td>
            </tr>
			<tr>
              <td class="label">State</td>
              <td>
			  <?php
				echo form_input(array('name'=>'state','id'=>'state','maxlength'=>50,'size'=>40,'value'=>$user_data['state'] ));
				?>
			  </td>
            </tr>
			<tr>
              <td class="label">Zip Code</td>
              <td>
			  <?php
				echo form_input(array('name'=>'zipcode','id'=>'zipcode','maxlength'=>50,'size'=>40,'value'=>$user_data['zipcode'] )) ;
				?>
			  </td>
            </tr>
			<tr>
              <td class="label">Country</td>
              <td>
			  <?php
				echo form_input(array('name'=>'country','id'=>'country','maxlength'=>50,'size'=>40,'value'=>$user_data['country'] ));
				?>
			  </td>
            </tr>
			<tr>
              <td class="label">Fax</td>
              <td>
			  <?php
				echo form_input(array('name'=>'fax','id'=>'fax','maxlength'=>50,'size'=>40,'value'=>$user_data['fax'] )) ;
				?>
			  </td>
            </tr>
            <tr>
              <td class="label">&nbsp;</td>
              <td>
				<?php
					echo form_submit(array('name' => 'btnSubmit', 'id' => 'btnSubmit','content' => 'Submit','class'=>'button-input'), 'Submit');
				?>
			       </td>
            </tr>
          </table>
		  
		  <?php
			if($this->session->userdata('user_packages_id')){
			?>
				 <input type="hidden" name="packageId" value="<?php echo $this->session->userdata('user_packages_id');  ?>">
			<?php
			}
			echo form_hidden('action','save');
			echo form_close();
		?>
      </div>
    </div>
  </div>
  <!--[/body]-->