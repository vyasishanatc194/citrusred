<?php
/************************Plugin excel reading for converting xls file to csv file*************************/

 if (!defined('BASEPATH')) exit('No direct script access allowed');

function convert_xls_to_csv($file_path,$target_path)
{
    require_once("excelreader/excel_reader2.php");    
    $excel = new Spreadsheet_Excel_Reader();
    $excel->setOutputEncoding('CP1251');
    $excel->read($file_path);
    $x=1;
    $sep = ",";
    ob_start();
    while($x<=$excel->sheets[0]['numRows']) {
     $y=1;
     $row="";
     while($y<=$excel->sheets[0]['numCols']) {
         $cell = isset($excel->sheets[0]['cells'][$x][$y]) ? $excel->sheets[0]['cells'][$x][$y] : '';
         $row.=($row=="")? "".$cell."":"".$sep."".$cell."";
         $y++;
     } 
     echo $row."\n"; 
     $x++;
    }
	unlink($file_path);
    $fp = fopen($target_path,'w');
    fwrite($fp,ob_get_contents());
    fclose($fp);
    ob_end_clean();
	
}
?>