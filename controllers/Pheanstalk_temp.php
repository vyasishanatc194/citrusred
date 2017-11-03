<?php
/************About Us class********************/
class Pheanstalk_temp extends CI_Controller
{
	protected $pheanstalk;
	protected $logpath;
	
	function __construct(){
        parent::__construct();
		$this->load->helper('pheanstalk_init');
		$this->pheanstalk = new Pheanstalk('127.0.0.1'); 
		$this->logpath = $this->config->item('campaign_files');	
	}
	
	function index(){	
		 while(1){
			// dynamic tubes and the jobs sizes
			$tube_name = 'campaign-'.time();
			$tube_size = rand(5,20);
			$this_tube_content = '';
			for($i=0; $i< $tube_size; $i++) {
			  
			  $job = array('jid'=>rand(),'dtnow'=>date('Y-m-d H:i:s'),'msg'=>'something to say as '.$i);
			  $job_data = json_encode($job);
			  $this_tube_content .= "<br/>".$job_data;
			  $this->pheanstalk->useTube($tube_name)->put($job_data);
			  #echo "pushed: " . $job_data . "\n";
			}
			
		}
	} 
	function workers(){
		$this->log('starting');
		$cnt = 0;
		
		while(1) {
			$arrTubes = $this->pheanstalk->listTubes();
			if(count($arrTubes) > 0){
				// ALL THE TUBES ARE ADDED TO WATCHLIST				
				foreach($arrTubes as $thisTube)	{
				  $job = $this->pheanstalk->watch($thisTube);				  
				}  
				// JOB WILL BE FETCHED FROM ANY OF THE TUBES 				
				$job = $this->pheanstalk->ignore('default')->reserve();
				
				  $job_encoded = json_decode($job->getData(), false);
				  
				  // job will be added to the log file at /rcdata/campaign/
				  $this->log('job:'.print_r($job_encoded, 1));
				 
				  $this->pheanstalk->delete($job);
				  $cnt++;

				  $memory = memory_get_usage();

				  $this->log('memory used:' . $memory);

				  if($memory > 1000000000) {
					$this->log('exiting run due to memory limit');
					exit;
				  }				
			}
		}
		$this->log('total jobs worked upon ='.$cnt);
		$this->log('ending');
	}
	
	private function log($txt) {
		file_put_contents($this->logpath . 'log_worker.txt', $txt . "<br/>\n", FILE_APPEND);
	} 
	
}
/* End of file */
?>
