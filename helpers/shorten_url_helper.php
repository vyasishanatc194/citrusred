<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_shorten_url'))
{
    function get_shorten_url()
    {
        // Outside of controller
		/* $CI =& get_instance();
		#Load configuration model
		$CI->load->model('ConfigurationModel');
		#Fetch domain name
		$site_configuration_array=$CI->ConfigurationModel->get_site_configuration_data(array('config_name'=>'domain_name')); 
		$domain_name=$site_configuration_array[0]['config_value'];
		 */
		 
		 # this will be used when a different domain name will be used to show the campaign.
		 # Right now no such plan is there.
		
		return base_url();
    }
} 
/* End of file shorten_url_helper.php */
/* Location: ./system/helpers/shorten_url_helper.php */