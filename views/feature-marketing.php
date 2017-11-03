    <?php
    $this->load->helper('url');
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, $this->config->item('redirect_url')) > 0) {
        redirect('https://getredcappi.com/features/');
    }
    ?>
<section role="main" class="main-container content-page features" itemscope>
  <section>
    <h3 itemprop="alternativeHeadline">Manage and Grow Your Email Lists</h3>
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/contact-list.png" alt="Contact List" class="feature-image" style="display:inline" />
  </section>
  <div class="content key-points"><div itemprop="featureList">
    <article itemprop="description">Simply manage all your email contacts in one place</article>
    <article itemprop="description">Collect &amp; import your email contacts with ease from various sources</article>
    <article itemprop="description">Full Featured API to sync your contact database </article>
  </div></div>
  <section>
    <h3 itemprop="alternativeHeadline">Easily Create an Email Newsletter</h3>
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/diy-editor.jpg" alt="Contact List" class="feature-image" />
  </section>
  <div class="content key-points"><div itemprop="featureList">
    <article itemprop="description">Absolutely no technical skills necessary. Promise.</article>
    <article itemprop="description">Choose from our variety of fresh color themes or craft your own.</article>
    <article itemprop="description">Extend your reach by adding social media quick links in your emails.</article>
    <article itemprop="description">Choose from dozens of email banners or upload your own, and even add your own logo on it...</article>
    <article itemprop="description">Our easy-to-use drag &amp; drop email builder lets you create amazing looking emails in just minutes, that are mobile friendly too :)</article>
  </div></div>
  <section>
    <h3 itemprop="alternativeHeadline">All the power you want, in an organized fashion</h3>
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/campaign-list.png" alt="Contact List" class="feature-image" />
  </section>
  <div class="content key-points"><div itemprop="featureList">
    <article itemprop="description">Send your email and we'll take care of the rest... We're committed to optimizing deliverability by virtue of its permission-based platform, first-rate working relationships with varying ISP's, feedback loops, and attention to white list importance and reputation.</article>
    <article itemprop="description">Our articlest management system automatically takes care of unsubscribes and bounces for you, so that you will always be in compliance with SPAM laws.</article>
  </div></div>
  <section>
    <h3 itemprop="alternativeHeadline">Easy to setup and use Autoresponders</h3>
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/autoresponder-list.png" alt="Contact List" class="feature-image" />
  </section>
  <div class="content key-points"><div itemprop="featureList">
    <article itemprop="description">Create and schedule Autoresponders to welcome new subscribers and automatically send a series of emails in the following days</article>
    <article itemprop="description">We include a one-click unsubscribe link in every email newsletter, so that you are always in compliance with CAN -SPAM laws.</article>
    <article itemprop="description">With our Forward-To-A-Friend Feature, your recipients can quickly forward your email campaign to their own friends too...</article>
    <article itemprop="description">You can send it now or schedule your emails to be delivered later at a specific time and date.</article>
  </div></div>
  <section>
    <h3 itemprop="alternativeHeadline">Realtime Campaign Results</h3>
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/stat-list.png" style="border-top: 1px solid #ddd; border-right: 1px solid #F4E5E3" alt="Contact List" class="feature-image" />
  </section>
  <div class="content key-points"><div itemprop="featureList">
    <article itemprop="description">Our Stats allow you to see the number of opens, clicks, bounces and unsubscribes for any of your email campaigns.</article>
    <article itemprop="description">You can also see exactly who is opening and what links they clicked on.</article>
    <article itemprop="description">Use our Stats to better evaluate your previous email campaigns, to see what really works.</article>
  </div></div>
  <section>
    <h3 itemprop="alternativeHeadline">Begin adding more subscribers with built-in Sign Up Forms!</h3>
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/signup-list.png" alt="Contact List" class="feature-image" />
  </section>
  <div class="content key-points"><div itemprop="featureList">
    <article itemprop="description">Add customizable email Signup Forms (that match your brand), to your website and Facebook page</article>
  </div></div>
  <section>
    <h3 itemprop="alternativeHeadline">Seamlessly integrates with Facebook, Twitter and Google Analytics</h3>
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/extra-list.png" alt="Contact List" class="feature-image" />
  </section>
  <div class="content key-points"><div itemprop="featureList">
  </div></div>
  <h3 itemprop="alternativeHeadline">Create a Free Account, No Credit Card Required</h3>
  <div class="sign-up-bar content">
    <?php echo form_open('user/register/'); ?>
      <input placeholder="Email..." type="text" class="text" name="email" autocorrect="off" autocapitalize="off" /><input placeholder="Username..." type="text" class="text" name="username" autocorrect="off" autocapitalize="off" /><input placeholder="Password..." type="password" class="text" name="password" /><input value="Get Started" name="btnRegister" class="btn" type="submit" />
    </form>
  </div>
</section>
<script type="text/javascript">
  $(function() {
    var imgList = {
      $el : $(".feature-image"),
      count : 1
    };
    var showImage = function(x) {
      $(imgList.$el.get(x)).fadeIn(1300);
      imgList.count++;
      return true;
    };
    var checkScroll = function(self) {
      var top = $(window).scrollTop() + $(window).height();

      if(top > 852) {
        imgList[1] = imgList[1] || showImage(imgList.count);

        if(top > 1797) {
          imgList[2] = imgList[2] || showImage(imgList.count);

          if(top > 2577) {
            imgList[3] = imgList[3] || showImage(imgList.count);

            if(top > 3450) {
              imgList[4] = imgList[4] || showImage(imgList.count);

              if(top > 4175) {
                imgList[5] = imgList[5] || showImage(imgList.count);

                if(top > 4890) {
                  imgList[6] = imgList[6] || showImage(imgList.count);
                }
              }
            }
          }
        }
      }
    };
    checkScroll();
    $(window).scroll(checkScroll);
  });
</script>
<script type="text/javascript">
var _ouibounce = ouibounce( $('#ouibounce-modal')[0], { sensitivity: 20, aggressive: false,sitewide: false,timer: 10, delay: 10,cookieName: 'rcfeature', callback: function() { console.log('ouibounce fired!'); } });
</script>