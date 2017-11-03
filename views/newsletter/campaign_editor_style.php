<?php


  load_class('CssMin','libraries', '');
$arrCssFiles = array(
			'css/jquery.fastconfirm.css',
			'css/blitzer/jquery-ui-1.8.10.custom.css',
			'js/fancybox/jquery.fancybox-1.3.4.css',
			'facebox/facebox.css',
			'css/qtip.demo.css',
			'css/qtip.min.css'

			);
$modified_time = 0;
foreach($arrCssFiles as $cssfile){
		$css_output .= (file_get_contents(FCFOLDER . '/webappassets/'.$cssfile))."\n\n\n";
		$modified_time = max(filemtime(FCFOLDER . '/webappassets/'.$cssfile), $modified_time);
}
 $css_output = CssMin::minify($css_output);

header("Content-type: text/css");

echo ($css_output);

?>
.toolbar div {
	position: relative;
}

.toolbar div .colorTip {
	-webkit-transition: all 0.2s 1s ease-in-out;
	-moz-transition: all 0.2s 1s ease-in-out;
	transition: all 0.2s 1s ease-in-out;
	margin-left: -32px;
	visibility: hidden;
	display: block;
	opacity: 0;
}

.toolbar div:hover .colorTip {
	visibility: visible;
	opacity: 1;
}

#body_main{
	width:595px !important; color: #333333;
	/* height:250px !important; */
}
.empty_block
{
	background:url("../../../webappassets/images/drop-email-content.png?v=6-20-13") no-repeat scroll center center transparent;
	width:100%;
	height:300px;
	background-position: center 20px;
}
.container-div{
	border-width:15px 5px 5px 5px;
	border-style:solid;
	border-color:transparent;
	width:100%;	
	text-align:center;
}
.highlighted{
	border-width:15px 5px 5px 5px;
	border-style:solid;
	border-color:#08C;
}

.handler_ul_div{position:relative !important;}

ul.handler{
padding:5px 0px 2px 0px !important;
margin-top:-18px;
position:absolute;
right:0px;
z-index:1000;
width:100%;
float:left;
display:none;
}
ul.handler li{
list-style-type: none;
padding: 0px;
margin:0px;
float:right;
display:block;

}

.div_border {
	display: block;
	position: absolute;
	top: -1px;
	bottom: -1px;
	right: -1px;
	left: -1px;
}
ul.handler li a{ font-size:11px;
color:#fff;
display:block;
padding: 0px 0px 0px 10px;
margin:0px;
text-align:right;
vertical-align:top;
line-height:11px;
}

ul.handler li.drag-center{ float:none !important; text-align: center !important; margin:0 auto !important ;}
ul.handler li.drag-center a{ float:none !important; text-align:center !important; margin:0 auto !important ;}

//ul.handler li.drag_social_li {padding-left:30px !important;}
.social_li{
	padding-left:5px;padding-right: 5px;
	padding-top:0px;padding-bottom: 0px;
}
.drag_handler{
	margin-top:1px;
	cursor:move;
	width:10px;
}

.footer_menu ul{
	width:27px !important;
}

.clone_image{
	cursor:pointer;
}
.close-clone-link{
	cursor:pointer;
}
.image_caption{display: block;
font-size: 11px;
padding-bottom: 0px;
padding-left: 0px;
text-align: left;
}
.youtube_image_caption{
	display:block;
}
.option_image-caption{
	cursor:pointer;
}
.empty_header{
	width:100%;
	float:left
}
.empty_header img{
	width:100%;
}
.menu{float:right;z-index:12;position :absolute !important;top:0 !important;right:0 !important;}
.header_div{
	position :relative;
}
#fancybox-content{
	overflow-y:auto !important;
}

.ui-slider-horizontal{
	background:#eee !important;
}
#slider{
width:120px;
float:left;
border:1px solid #aaa;
margin:0px 0 0 10px;
height:5px;
-moz-box-shadow: 0 1px 3px rgba(0,0,0,0.25);
-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.25);
box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
#slider a{
background-image: -moz-linear-gradient(top, #ff3019, #cf0404);
background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ff3019), to(#cf0404));
background-image: -webkit-linear-gradient(top, #ff3019, #cf0404);
background-image: -o-linear-gradient(top, #ff3019, #cf0404);
background-image: linear-gradient(to bottom, #ff3019, #cf0404);
background-repeat: repeat-x;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffff3019', endColorstr='#ffcf0404', GradientType=0);
background-color: #cf0404;
border-color: #51a351 #51a351 #387038;
border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
margin-top:-3px;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
border-radius: 3px;
}

#slider a:active {
	background-image: none !important;
  -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
  -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
}
.thin-border-radius{
	border: 1px solid #FF0000 !important;
    border-radius: 2px 2px 2px 2px;
}
#header{
	width:100%;
	height:Auto;
	z-index:30;
}
.header_img{
	width:595px;
	height:auto;
	display:block;
}
.edit_offer_div{
	background-color:#fff;
}
#logo{
	z-index:15 !important;
}
.text-paragraph-container p,.edit_offer p{
  padding:0px;
  margin:0px;
}
.text-paragraph-container h1{
	font-size:30px;
}
.text-paragraph-container table{
	width:auto !important;
	height:auto !important;
}
.text-paragraph-container h1,h2,h3,h4,h5,h6{
	padding:0px !important;
	margin:0px !important;
}
.text-paragraph-container{
	font-size:14px;
}
.ui-state-helper {
    border: 1px dashed #808080 !important;
    color: #444444;
}
.text-paragraph-container ul li, ol li{ list-style-position: inside; }
