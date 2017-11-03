<?php
/************About Us class********************/
class About extends CI_Controller
{
	function __construct(){
        parent::__construct();			
		force_ssl();		
	}
	function index()
	{
		
		$current_url=current_url();	#Get Current url
		
		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true);
		//Loads header, About us and footer view.
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'About Us','show_bottom_bar'=>true));
		$this->load->view('about-us');
		$this->load->view('footer_outer');	
	}
    function test3($url){
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$a = curl_exec($ch);
			curl_close( $ch ); 
			// the returned headers
			$headers = explode("\n",$a);
			// if there is no redirection this will be the final url
			$redir = $url;
			// loop through the headers and check for a Location: str
			$j = count($headers);
			for($i = 0; $i < $j; $i++){
			// if we find the Location header strip it and fill the redir var       
			if(strpos($headers[$i],"Location:") !== false){
					$redir = trim(str_replace("Location:","",$headers[$i]));
					break;
				}
			}
			
		// do whatever you want with the result
		return $redir;
    }
   function test4(){
   		
   		$x = 'http://businessweekfx.com/rockthestock';
   		for($i=1;$i < 4; $i++){ 
			$x = $this->test3($x);
		}
		echo $x;
   }
 
 

	function test(){
	 
	//$url = 'http://www.forexstrategieswork.com/daily-technical-analysis-june-25-2015/?subid=gr250615';
	//$url = 'https://s3.amazonaws.com/clk111/oclube1porcento1.html';
	$url = 'http://businessweekfx.com/rockthestock';

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0"); // Necessary. The server checks for a valid User-Agent.
	curl_exec($ch);
echo $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
echo $last_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL );
	$response = curl_exec($ch);
	preg_match_all('/^Location:(.*)$/mi', $response, $matches);
	curl_close($ch);
//print $response;
	echo !empty($matches[1]) ? trim($matches[1][0]) : 'No redirect found';
	}
	function getFinalURL(){
	
	echo $this->curl_last_url('http://yournextmillenniumcard.com/?lid=20130764555953b4885f9a230', 5);
	}
	function curl_last_url( $url = '', $maxredirect = null) { 
		//$url = 'http://yournextmillenniumcard.com/?lid=20130764555953b4885f9a230';
		//$url = 'http://businessweekfx.com/rockthestock';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0"); // Necessary. The server checks for a valid User-Agent.
	$mr = $maxredirect === null ? 5 : intval($maxredirect); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
		if ($mr > 0) { 
			$mr;
			$newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); 

			$rch = curl_copy_handle($ch); 
			curl_setopt($rch, CURLOPT_HEADER, true); 
			curl_setopt($rch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0"); // Necessary. The server checks for a valid User-Agent.
			curl_setopt($rch, CURLOPT_NOBODY, true); 
			curl_setopt($rch, CURLOPT_FORBID_REUSE, false); 
			curl_setopt($rch, CURLOPT_RETURNTRANSFER, true); 
			do { 
				curl_setopt($rch, CURLOPT_URL, $newurl); 
				$header = curl_exec($rch); 
				if (curl_errno($rch)) { 
					$code = 0; 
				} else { 
					$code = curl_getinfo($rch, CURLINFO_HTTP_CODE); 					 
					if ($code == 301 || $code == 302) { 
						preg_match('/Location:(.*?)\n/', $header, $matches); 
						$newurl = trim(array_pop($matches)); 
					} else { 
						$code = 0; 
					} 
				} 
			} while ($code && --$mr); 
			curl_close($rch); 
			if (!$mr) { 
				if ($maxredirect === null) { 
					trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING); 
				} else { 
					$maxredirect = 0; 
				} 
				return false; 
			} 
			curl_setopt($ch, CURLOPT_URL, $newurl); 
		} 
		return $newurl; 
	}
}
/* End of file */
?>
