<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* This library assumes that you have already loaded the default CI Upload Library seperately
* 
* Functions is based upon CI_Upload, Feel free to modify this 
*   library to function as an extension to CI_Upload
* 
* Library modified by: Alvin Mites
* http://www.mitesdesign.com
* 
*/
class MY_Encrypt extends CI_Encrypt
{

    function encode($string, $key="", $url_safe=TRUE)
    {
        $ret = parent::encode($string, $key);

        if ($url_safe)
        {
            $ret = strtr(
                    $ret,
                    array(
                        '+' => '.',
                        '=' => '-',
                        '/' => '~'
                    )
                );
        }

        return $ret;
    }


    function decode($string, $key="")
    {
        $string = strtr(
                $string,
                array(
                    '.' => '+',
                    '-' => '=',
                    '~' => '/'
                )
            );

        return parent::decode($string, $key);
    }
}
?>
