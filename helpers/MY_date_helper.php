<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 
/**
 *Convert the time in GMT timestamp into  user's local time zone timestamp
 * @param time $gmttime
 * @param string $timezoneRequired
 * $gmttime should be in timestamp format like '02-06-2009 09:48:00.000'
 * $timezoneRequired sholud be a string like 'Asia/Calcutta' not 'IST' or 'America/Chicago' not 'CST'
 * return timestamp format like '02-06-2009 09:48:00' (m-d-Y H:i:s) Can also change this format
 * $timestamp = $date->format("m-d-Y H:i:s"); decide the return format
 *By: Subhranil Dalal , Mindfire Solutions                            
*/
 

function getGMTToLocalTime($gmttime,$timezoneRequired){
	$dt = new DateTime($gmttime.' GMT');
	$dt->setTimeZone(new DateTimeZone($timezoneRequired));
	return $dt->format('Y-m-d H:i:s');
}

function getLocalToGMT($gmttime){
	$dt = new DateTime(trim($gmttime));
	$dt->setTimeZone(new DateTimeZone('GMT'));
	return $dt->format('Y-m-d H:i:s');
}

/*
* Following function are not in use
*/
function ConvertGMTToLocalTimezone($gmttime,$timezoneRequired)
{
    $system_timezone = date_default_timezone_get();

    date_default_timezone_set("GMT");
    $gmt = date("Y-m-d h:i:s A");

    $local_timezone = $timezoneRequired;
    date_default_timezone_set($local_timezone);
    $local = date("Y-m-d h:i:s A");

    date_default_timezone_set($system_timezone);
    $diff = (strtotime($local) - strtotime($gmt));

   // $date = new DateTime($gmttime);
    $date->modify("+$diff seconds");
    $timestamp = $date->format("m-d-Y H:i:s");
    return $timestamp;
}
 
/**
*Use: ConvertGMTToLocalTimezone('2009-02-05 11:54:00.000','Asia/Calcutta');
*Output: 02-05-2009 17:24:00 || IST = GMT+5.5
*/
 /**
 *Convert the time in user's local time zone timestamp into GMT timestamp  
 * @param time $gmttime
 * @param string $timezoneRequired
 * $gmttime should be in timestamp format like '02-06-2009 09:48:00.000'
 * $timezoneRequired sholud be a string like 'Asia/Calcutta' not 'IST' or 'America/Chicago' not 'CST'
 * return timestamp format like '02-06-2009 09:48:00' (m-d-Y H:i:s) Can also change this format
 * $timestamp = $date->format("m-d-Y H:i:s"); decide the return format 
 *By: Subhranil Dalal , Mindfire Solutions                             Date:06/02/2009
*/

function ConvertLocalTimezoneToGMT($gmttime,$timezoneRequired)
{
    $system_timezone = date_default_timezone_get();
 
    $local_timezone = $timezoneRequired;
    date_default_timezone_set($local_timezone);
    $local = date("Y-m-d h:i:s A");
 
    date_default_timezone_set("GMT");
    $gmt = date("Y-m-d h:i:s A");
 
    date_default_timezone_set($system_timezone);
    $diff = (strtotime($gmt) - strtotime($local));
 
    $date = new DateTime($gmttime);
    $date->modify("+$diff seconds");
    $timestamp = $date->format("m-d-Y H:i:s");
    return $timestamp;
}
 
/**
*Use: ConvertLocalTimezoneToGMT('2009-02-05 17:24:00.000','Asia/Calcutta');
*Output: 02-05-2009 11:54:00 ||  GMT = IST-5.5
*/
 
 
 
 
/**
 *Convert the time in user's local time zone timestamp into another time zone timestamp
 * @param time $gmttime
 * @param string $timezoneRequired
 * $gmttime should be in timestamp format like '02-06-2009 09:48:00.000'
 * $timezoneRequired sholud be a string like 'Asia/Calcutta' not 'IST' or 'America/Chicago' not 'CST'
 * return timestamp format like '02-06-2009 09:48:00' (m-d-Y H:i:s) Can also change this format
 * $timestamp = $date->format("m-d-Y H:i:s"); decide the return format 
 *By: Subhranil Dalal , Mindfire Solutions                             Date:06/02/2009.
*/


function ConvertOneTimezoneToAnotherTimezone($time,$currentTimezone,$timezoneRequired)
{
    $system_timezone = date_default_timezone_get();
    $local_timezone = $currentTimezone;
    date_default_timezone_set($local_timezone);
    $local = date("Y-m-d h:i:s A");
 
    date_default_timezone_set("GMT");
    $gmt = date("Y-m-d h:i:s A");
 
    $require_timezone = $timezoneRequired;
    date_default_timezone_set($require_timezone);
    $required = date("Y-m-d h:i:s A");
 
    date_default_timezone_set($system_timezone);

    $diff1 = (strtotime($gmt) - strtotime($local));
    $diff2 = (strtotime($required) - strtotime($gmt));

    $date = new DateTime($time);
    $date->modify("+$diff1 seconds");
    $date->modify("+$diff2 seconds");
    $timestamp = $date->format("m-d-Y H:i:s");
    return $timestamp;
}
 
 

/* End of file My_date_helper.php */
/* Location: ./system/helpers/My_date_helper.php */