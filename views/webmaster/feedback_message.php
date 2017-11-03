<div class="tblheading">Feedback Message</div>
<div id="messages">
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
<?php 
echo '<table class="tbl_forms" >';
echo "<tr><td>";
echo "<b>Message:</b><br/>"; 
echo  $message."</td>";

echo "</tr>";
echo "</table>";
?>
</div>