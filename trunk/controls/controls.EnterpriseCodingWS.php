<?php
#####################################################################################
#        Alumni-OnLine: Alumni Organization Web Management System                   #
#####################################################################################
#                                                                                   #
# Copyright © 2004 by Fatih BOY		                                                #
# http://alumni-online.enterprisecoding.com/                                        #
#                                                                                   #
# This program is free software. You can redistribute it and/or modify              #
# it under the terms of the GNU General Public License as published by              #
# the Free Software Foundation; either version 2 of the License.                    #
#####################################################################################

require_once('controls.Settings.php');
require_once(DIR_CONTRIB.'nuSoap'.DIRECTORY_SEPARATOR.'nusoap.php');

/*
Class: SmartCodingWS
	Comminicates with Enterprise Coding web service
*/
class SmartCodingWS {
	var $client;
	var $wsdlFile   = 'http://tracker.enterprisecoding.com/TrackerService.asmx?WSDL';
	var $error      = '';
	
	function SmartCodingWS(){
		$this->client = new soapclient($this->wsdlFile, true);

		$err = $this->client->getError();
		if ($err) {
			$error = "Constructor error : $err";
		}
	}
	
	function submitError($errortype, $errmsg, $filename, $linenum){
		$this->error = '';
		
		$content  = "<SubmitBug xmlns='http://www.enterprisecoding.com/webservices/'>";
		$content .= "	<projectID>".WS_PROJECT_CODE."</projectID>";
		$content .= "   <componentID>".WS_COMPONENT_ID."</componentID>";
		$content .= "   <versionID>".WS_VERSION_ID."</versionID>";
		$content .= "   <issueType>".WS_ISSUE_TYPE."</issueType>";
		$content .= "   <title>$errortype at $filename on line $linenum</title>";
		$content .= "   <description>".APPLICATION_NAME." Version : ".APPLICATION_VERSION."&lt;br&gt;$errmsg</description>";
		$content .= "</SubmitBug>";
		
		$result = $this->client->call('SubmitBug', $content);
		
		// Check for a fault
		if ($this->client->fault) {
			$this->error = $result;
		} else {
			// Check for errors
			$err = $this->client->getError();

			if ($err) {
				$this->error = "Error : $err";
			} else {
				return $result;
			}
		}
	
		return '';
	}
}
?>
