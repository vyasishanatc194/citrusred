 <!--[body]-->
     <?php
    $this->load->helper('url');
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, $this->config->item('redirect_url')) >0) {
        redirect('https://getredcappi.com/pricing/');
    }
    ?>
<section role="main" class="main-container content-page" itemscope>
  <h2 itemprop="headline">Pricing</h2>
  <div class="content key-points">
    <div>
        <h3 itemprop="alternativeHeadline">Start sending emails for Free!</h3>
        <p itemprop="description">All of our plans are billed monthly and are based on the total number of contacts in your account.</p>
    </div>
    <div>
      <table>
        <tr>
          <td>
            <strong>Contacts</strong>
          </td>
        <?php
			for($i=0;$i<8;$i++){
				echo '<td>Up to '. number_format($packages[$i]['package_max_contacts']).'</td>';
			}
		?>
        </tr>
        <tr>
          <td>
            <strong>Monthly Price</strong>
          </td>
        <?php
			for($i=0;$i<8;$i++){
				if($packages[$i]['package_price']==0.00)
					echo '<td><strong> Free! </strong></td>';
				else
					echo '<td><strong itemprop="price">$'.number_format($packages[$i]['package_price'],0).'</strong><meta itemprop="priceCurrency" content="USD" /></td>';
			}
        ?>
        </tr>
		 <tr>
          <td>
            <strong>Yearly Price</strong>
          </td>
        <?php
			echo '<td><strong> Free! </strong></td>';
			for($i=0;$i<7;$i++){
				
					echo '<td><strong itemprop="price">$'.number_format($packages_yearly[$i]['package_price'],0).'</strong><meta itemprop="priceCurrency" content="USD" /></td>';
			}
        ?>
        </tr>
        <tr>
          <td>
            <strong>Send Limit</strong>
          </td>
          <td colspan="8" class="send-limit">Unlimited *</td>
        </tr>
      </table>
    </div>
    <div>
      <h3 itemprop="alternativeHeadline">Pro Volume</h3>
      <p itemprop="description">No Contracts! Cancel Anytime!</p>
    </div>
    <div>
      <table>
        <tr>
          <td>
            <strong>Contacts</strong>
          </td>
          <?php
            for($i=8;$i<16;$i++){
				echo '<td>Up to '. number_format($packages[$i]['package_max_contacts']).'</td>';
			}
          ?>
        </tr>
        <tr>
          <td>
            <strong>Monthly Price</strong>
          </td>
          <?php
           for($i=8;$i<16;$i++){
				echo '<td><strong itemprop="price">$'.number_format($packages[$i]['package_price'],0).'</strong><meta itemprop="priceCurrency" content="USD" /></td>';
			}
          ?>
        </tr>
		<tr>
          <td>
            <strong>Yearly Price</strong>
          </td>
          <?php
           for($i=7;$i<15;$i++){
				echo '<td><strong itemprop="price">$'.number_format($packages_yearly[$i]['package_price'],0).'</strong><meta itemprop="priceCurrency" content="USD" /></td>';
			}
          ?>
        </tr>
        <tr>
          <td>
            <strong>Send Limit</strong>
          </td>
          <td colspan="8" class="send-limit">Unlimited *</td>
        </tr>
      </table>
    </div>
    <div>
      <h3 itemprop="alternativeHeadline">Lists over 500k</h3>
      <p itemprop="description"><a href="<?php echo  base_url()."contact";?>">Contact us</a> for pricing on lists over 500,000 contacts or for any other questions or needs.</p>
      <p itemprop="description" style="font-size:10px;">*Individual sending limits may apply.</p>
    </div>
  </div>
  
  <div class="sign-up-bar content">
    <?php echo form_open('user/register/'); ?>
      <input placeholder="Email..." type="text" class="text" name="email" autocorrect="off" autocapitalize="off" /><input placeholder="Username..." type="text" class="text" name="username" autocorrect="off" autocapitalize="off" /><input placeholder="Password..." type="password" class="text" name="password" /><input value="Get Started" name="btnRegister" class="btn" type="submit" />
    </form>
  </div>
</section>
<!--[/body]-->
<script type="text/javascript">
var _ouibounce = ouibounce( $('#ouibounce-modal')[0], { sensitivity: 20, aggressive: false,sitewide: false,timer: 10, delay: 10,cookieName: 'rcpricing', callback: function() { console.log('ouibounce fired!'); } });
</script>