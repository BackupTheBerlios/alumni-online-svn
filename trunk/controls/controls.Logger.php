<?php
#####################################################################################
#        Alumni-OnLine: Alumni Organization Web Management System                   #
#####################################################################################
#                                                                                   #
# Copyright © 2004 by Fatih BOY		                                            #
# http://alumni-online.enterprisecoding.com/                                        #
#                                                                                   #
# This program is free software. You can redistribute it and/or modify              #
# it under the terms of the GNU General Public License as published by              #
# the Free Software Foundation; either version 2 of the License.                    #
#####################################################################################

require_once('controls.Settings.php');
require_once('controls.EnterpriseCodingWS.php'); //Include web service proxy class 


/*
Class: logger
	Handles and logs system exceptions.
*/

class logger {
	// define an assoc array of error string 
	// in reality the only entries we should 
	// consider are 2,8,256,512 and 1024 
	var $errortype = array ( 
				   1   =>  "Error", 
				   2   =>  "Warning", 
				   4   =>  "Parsing Error", 
				   8   =>  "Notice", 
				   16  =>  "Core Error", 
				   32  =>  "Core Warning", 
				   64  =>  "Compile Error", 
				   128 =>  "Compile Warning", 
				   256 =>  "User Error", 
				   512 =>  "User Warning", 
				   1024=>  "User Notice" 
				   );
				   
	var $oldHandler;

   function logger() {
		error_reporting(ERROR_REPORTING_LEVEL);
   		$this->oldHandler = set_error_handler(array(&$this,'logHandler'));
   }
   
   /*
	Function: logHandler
		Used to handle php generated errors and log them
		
		Parameters:
			$errno		- Error number.
			$errmsg		- Error message.
			$filename	- File name that error occured.
			$linenum	- Line number at which error occured
			$vars		- Variables that has been defined when error occured.
		
		Note:
			This function will be called by php engine. Generates an xml log 
		and saves it to the 'logs/error_DATETIME.xml' file.
	*/
	function logHandler($errno, $errmsg, $filename, $linenum, $vars) {
	   
	   $filename = str_replace(DIR_APPLICATION_BASE, '', $filename);
		
	   if(!(substr($filename, 0, 13)=="contrib\myXML" && $errno==8)){
		   $this->logError($errno, $errmsg, $filename, $linenum);
		   
		   //display error message
		   echo $this->generateErrorMessage();
	   }
	}

	
        /*
	Function: logError
		Used to records application errors
		
		Parameters:
			$errno		- Error number.
			$errmsg		- Error message.
			$filename	- File name that error occured.
			$linenum	- Line number at which error occured
	*/
	function logError($errno, $errmsg, $filename, $linenum){
		// timestamp for the error entry 
		$dateTime = date("H:i:s");
				
		$err = "<error>\n"; 
	   	$err .= "\t<time>".$dateTime."</time>\n"; 
	   	$err .= "\t<errornum>".$errno."</errornum>\n"; 
	   	$err .= "\t<type>".$this->errortype[$errno]."</type>\n"; 
	   	$err .= "\t<message>".$errmsg."</message>\n"; 
	   	$err .= "\t<script>".$filename."</script>\n"; 
	   	$err .= "\t<line>".$linenum."</line>\n"; 
	   	$err .= "</error>\n"; 
	
		// save to the error log
	   error_log($err, 3, dirname(__FILE__).'/../logs/error_'.date("Y-m-d").'.xml');
	}
	

        /*
	Function: generateErrorMessage
		Generates user friendly error messages to inform user.
	*/
	function generateErrorMessage(){
		$result  = "<table width='100%'  border='0' cellspacing='0' cellpadding='0' class='ErrorTable'>\n";
	   	$result .= "  <tr>\n";
	   	$result .= "    <td width='9%' align='center' valign='middle'><img src='".DIR_TEMPLATES_SITE."default/images/error.gif'></td>\n";
	   	$result .= "    <td width='91%'>An error occured!<br>\n";
	   	$result .=  "    Please contact your system administrator for details </td>\n";
	   	$result .=  "  </tr>\n";
	   	$result .=  "</table>\n";
		
		return $result;
	}
	
        /*
	Function: submitErrorReport
		Submits given error details to SmartCoding tracker system.
		
		Parameters:
			$errno		- Error number.
			$errmsg		- Error message.
			$filename	- File name that error occured.
			$linenum	- Line number at which error occured
	*/
	function submitErrorReport($errno, $errmsg, $filename, $linenum){
		$smartcodingWS = new SmartCodingWS();
	
		if($smartcodingWS->error==''){
			$issueID = $smartcodingWS->submitError($this->errortype[$errno], htmlspecialchars($errmsg,  ENT_QUOTES), htmlspecialchars($filename,  ENT_QUOTES), htmlspecialchars($linenum,  ENT_QUOTES));
	
			if($smartcodingWS->error==''){
				return "http://tracker.enterprisecoding.com/Default.aspx?p=".WS_PROJECT_CODE."&i=" . $issueID;
			}
		}
	}	
}
?>
