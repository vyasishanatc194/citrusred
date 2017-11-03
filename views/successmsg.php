<div id="body-dashborad">
<div class="container">
      <div class="dashboard-home">
        <h1>Email Report</h1>
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
</div>
</div>
</div>
  <!--[/body]-->