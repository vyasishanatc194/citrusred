<?php $ci =& get_instance();?>

<link rel="prerender" href="/signup" />
<section role="main" class="main-container home" itemscope>
  <div class="header-message">
    <h2 class="easy-email-marketing">Easy Email Marketing.</h2>
    <h2 itemprop="alternativeHeadline">Create, send and track cool and hip looking emails and newsletters.</h2>
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/mean-it.png?v=6-20-13" id="mean-it" alt="Easy low price email marketing" />
  </div>
  <div id="hero-container">
    <div id="hero">
      <img src="<?php echo $this->config->item('webappassets');?>images/redesign/macbook-air.png?v=6-20-13" id="laptop" />
      <div id="video" itemprop="video">
        <div id="main-video"></div>
      </div>
      <ul>
        <li itemscope itemprop="featureList">
          <img itemprop="image" src="<?php echo $this->config->item('webappassets');?>images/redesign/customer-loyalty.png?v=6-20-13" alt="Customer Loyalty" />
          <strong itemprop="comment">Build customer trust and loyalty</strong>
          <p itemprop="description">RedCappi Email Marketing tool helps you build relationships with your customers to boost your sales.</p>
        </li>
        <li itemscope itemprop="featureList">
          <img itemprop="image" src="<?php echo $this->config->item('webappassets');?>images/redesign/retargeting.png?v=6-20-13" alt="Retargeting" />
          <strong itemprop="comment">Drive repeat business and brand awareness</strong>
          <p itemprop="description">Email Marketing is effectively targeted to keep your customers coming back.</p>
        </li>
        <li itemscope itemprop="featureList">
          <img itemprop="image" src="<?php echo $this->config->item('webappassets');?>images/redesign/social-icons.png?v=7-12-13" alt="Social Icons" />
          <strong itemprop="comment">Stir Social Media Buzz</strong>
          <p itemprop="description">Share your email newsletters on Facebook and Twitter to extend your reach with RedCappi.</p>
        </li>
      </ul>
    </div>
  </div>
  <h2 class="not-convinced free" itemprop="alternativeHeadline">Create a Free Account, No Credit Card Required!</h2>
  <div class="sign-up-bar content">
      <form action='user/register/' id="signup_form" method="post" onsubmit="" >
      <input placeholder="Email..." type="text" class="text" name="email" /><input placeholder="Username..." type="text" class="text" name="username" /><input placeholder="Password..." type="password" class="text" name="password" /><input value="Get Started" name="btnRegister" class="btn" type="submit" />
    </form>
  </div>
  <div class="header-message">
    <img itemprop="image" src="<?php echo $this->config->item('webappassets');?>images/redesign/no-cc.png?v=6-20-13" alt="No credit card required" id="no-cc" />
    <h1 class="not-convinced email-marketing-service-boost"><span itemprop="headline">Email Marketing Service To Help Boost Your Business</span></h1>
  </div>
  <div class="content key-points">
    <div>
      <img itemprop="image" src="<?php echo $this->config->item('webappassets');?>images/redesign/drag-drop-home-sub.jpg?v=6-20-13" />
      <div>
        <h3 itemscope itemprop="featureList">Drag-and-Drop Email Editor</h3>
        <p itemscope itemprop="featureList">
          Creating amazing Email Newsletters doesn't have to be complicated. RedCappi allows you to quickly create impressive emails and newsletters without any technical skills. Simply drag and drop images and content blocks where you want and design to your liking. With an email editor that's always in preview mode, you can edit and customize in real time.
        </p>
      </div>
    </div>
    <div>
      <img itemprop="image" src="<?php echo $this->config->item('webappassets');?>images/redesign/mobile-tablet.png?v=6-20-13" />
      <div>
        <h3 itemscope itemprop="featureList">Mobile Device Compatible <i class="icon-laptop"></i><i class="icon-tablet"></i><i class="icon-mobile-phone"></i></h3>
        <p itemscope itemprop="featureList">
          RedCappi makes it easy to create email promotions and newsletters that are mobile device compatible. That means, you can trust your emails are going to look great on even the smallest of screens. With more people on the go, an increasing number of emails are checked on mobile devices. We've got mobile optimization covered!
        </p>
      </div>
    </div>
  </div>
  <div class="logos" itemscope itemtype="http://schema.org/WebApplication">
    <img src="<?php echo $this->config->item('webappassets');?>images/redesign/partners/ex.png?v=6-20-13" itemprop="image" alt="RedCappi Featued in Examiner.com" /><img src="<?php echo $this->config->item('webappassets');?>images/redesign/partners/nyt.png?v=6-20-13" itemprop="image" alt="RedCappi Featued in New York Times" /><img src="<?php echo $this->config->item('webappassets');?>images/redesign/partners/sbt.png?v=6-20-13" itemprop="image" alt="RedCappi Featued in Small Business Trends" /><img src="<?php echo $this->config->item('webappassets');?>images/redesign/partners/sfv.png?v=6-20-13" itemprop="image" alt="RedCappi Featued in San Fernando Valley Business Journal" /><img src="<?php echo $this->config->item('webappassets');?>images/redesign/partners/ssv.png?v=6-20-13" itemprop="image" alt="RedCappi Featued in Shoestring Venture" />
    <span itemprop="mentions">RedCappi Featued in Examiner.com</span>
    <span itemprop="mentions">RedCappi Featued in New York Times</span>
    <span itemprop="mentions">RedCappi Featued in Small Business Trends</span>
    <span itemprop="mentions">RedCappi Featued in San Fernando Valley Business Journal</span>
    <span itemprop="mentions">RedCappi Featued in Shoestring Venture</span>
  </div>
  <h2 class="not-convinced" itemprop="alternativeHeadline">Start your Free Email Marketing Account and get your Campaign underway in Minutes!</h2>
  <div class="sign-up-bar content">
    <form action='user/register/' id="signup_form2" method="post" onsubmit="" >
      <input placeholder="Email..." type="text" class="text" name="email" /><input placeholder="Username..." type="text" class="text" name="username" /><input placeholder="Password..." type="password" class="text" name="password" /><input value="Get Started" name="btnRegister" class="btn" type="submit" />
    </form>
  </div>
</section>
<script src="https://www.youtube.com/player_api"></script>
<script type="text/javascript">
  // create youtube player
  var player;

  // autoplay video
  function onPlayerReady(event) {
        event.target.playVideo();
      if(!(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent))) {
      }
  }
  // when video ends
  function onPlayerStateChange(event) {
      if(event.data === 0) {
          document.getElementById("video").innerHTML = '<img src="<?php echo $this->config->item('webappassets');?>images/redesign/end-video-home.png" id="end-video" />';
      }
  }

  function onYouTubePlayerAPIReady() {
      player = new YT.Player('main-video', {
        height: '300',
        width: '535',
        videoId: 'czVXOMnWObw',
        playerVars: {
          'rel': 0,
          'controls': 0,
          'wmode': 'transparent',
          'showinfo': 0,
          'autohide': 1
        },
        events: {
          'onReady': onPlayerReady,
          'onStateChange': onPlayerStateChange
        }
      });
  }
  
  var _ouibounce = ouibounce( $('#ouibounce-modal')[0], { sensitivity: 20, aggressive: false,sitewide: false,timer: 10, delay: 10, cookieName: 'rchome',callback: function() { console.log('ouibounce fired!'); } });
</script>
