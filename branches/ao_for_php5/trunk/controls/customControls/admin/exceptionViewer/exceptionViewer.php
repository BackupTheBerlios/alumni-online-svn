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

$path[] = DIR_CONTRIB.'myXML';

if (substr(PHP_OS, 0, 3) == 'WIN') {
    $searchPath = implode(';', $path).';';
} else {
    $searchPath = implode(':', $path).':';
}

// Set the search path.
ini_set('include_path', $searchPath);
define('PEAR_ERROR_CALLBACK', "none");
define('PEAR_ERROR_RETURN', E_USER_ERROR);

require_once(DIR_CONTRIB.'myXML'.DIRECTORY_SEPARATOR.'XML_Preprocessor.php');
require_once(DIR_CONTRIB.'myXML'.DIRECTORY_SEPARATOR.'myDOM'.DIRECTORY_SEPARATOR.'myDOM.php');
require_once(DIR_CONTRIB.'myXML'.DIRECTORY_SEPARATOR.'myXPath'.DIRECTORY_SEPARATOR.'myXPath.php');
require_once(DIR_CONTRIB.'myXML'.DIRECTORY_SEPARATOR.'myXSLT'.DIRECTORY_SEPARATOR.'myXSLT.php');

/*
Class: exceptionViewer
	Displays AO exceptions. Administrative custom control that displays
Alumni-Online exceptions. Extends ControlBase for skin entegration.
	
	See Also:
		<ControlBase>
*/
class exceptionViewer extends ControlBase{
	
	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. 
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
	*/
	function actionPerform(&$skin, $moduleID){
		$skin->main->controlVariables["exceptionViewer"]['exceptionList'] = "";
		$skin->main->controlVariables["exceptionViewer"]['link'] 		  = "";
		$skin->main->controlVariables["exceptionViewer"]['tabId']         = $skin->main->selectedTab;
		$skin->main->controlVariables["exceptionViewer"]['logFiles']      = array();
		$skin->main->controlVariables["exceptionViewer"]['displayList']   = true;
		
		if(isset($_POST["event"]) && $_POST["event"]=="exceptionViewer:SubmitReport"){
			for($i=0;$i<sizeof($_POST["errors"]);$i++){
				$errorDetails = split("\\|", $_POST["errors"][$i]);
				$skin->main->controlVariables["exceptionViewer"]['link'] = $skin->main->logger->submitErrorReport($errorDetails[0], $errorDetails[3], $errorDetails[4], $errorDetails[5]);
			}
		}else if(isset($_POST["event"]) && $_POST["event"]=="exceptionViewer:DeleteLog"){
			for($i=0;$i<sizeof($_POST["logs"]);$i++){
				//Validate given file name
				if(ereg_replace ("([0-9]{4})-([0-9]{2})-([0-9]{2})", "", $_POST["logs"][$i])==""){
					$this->deleteLogFile($_POST["logs"][$i]);
				}else{
					trigger_error("Unable to delete log file : Given file name '{$_POST['logs'][$i]}' is invalid");
				}
			}
		}
		
		if(!isset($_GET["log"])){
			//Analyze logs directory
			$logs = array();
			$dir  = opendir(DIR_APPLICATION_BASE.'logs');
			while (false !== ($file = readdir($dir))) {
				if($file!="." && $file!=".."){
					$file = str_replace(array("error_", ".xml"), "", $file);
					$logs[$file] =$file;
				}
			}
			closedir($dir);
			
			$skin->main->controlVariables["exceptionViewer"]['logFiles'] = $logs;
			$skin->main->controlVariables["exceptionViewer"]['displayList']   = true;
		}else if(ereg_replace ("([0-9]{4})-([0-9]{2})-([0-9]{2})", "", $_GET["log"])==""){
			$skin->main->controlVariables["exceptionViewer"]['displayList']   = false;
			
			$filename = DIR_APPLICATION_BASE.'logs'.DIRECTORY_SEPARATOR.'error_'.$_GET["log"].'.xml';
			
			
			if(is_file($filename)){
				$handle   = fopen ($filename, "r"); 
				$contents = fread ($handle, filesize ($filename)); 
				fclose ($handle); 
	
				
				// Create new DOM documents for input, output and stylesheet data.
				$oDocument = new Document;
				$oStylesheet = new Document;
				$oOutDocument = new Document;
				
				// Create object of class XML_Preprocessor.
				$oXml = XML_Preprocessor::create(&$oDocument);
						
				//$oXml->parseFile(DIR_APPLICATION_BASE.'logs'.DIRECTORY_SEPARATOR.'error_'.$_GET["log"].'.xml');
				$oXml->parse("<errors>$contents</errors>");			
				
				$oStylesheet->parseFile(dirname(__FILE__).DIRECTORY_SEPARATOR.'exceptionViewer.xsl');
				
				// Create object myXPath.
				$oXPath =& myXPath::create(&$oDocument);
				
				// Create object myXSLT.
				$oXSLT = myXSLT::create(&$oDocument, &$oOutDocument, &$oStylesheet, &$oXPath);
				
				// Start translating.
				$oXSLT->translate();
				
				$skin->main->controlVariables["exceptionViewer"]['exceptionList'] = $oOutDocument->toString();
			}else{
				trigger_error("Unable to find log file : 'error_{$_GET['log']}.xml'");
			}
		}else{
			trigger_error("Invalid log file name supplied : '{$_GET['log']}'");
		}
	}
	
	/*	
		Function: deleteLogFile
			Deletes given log file from file system
			
		Parameters:
		
			$fileName  - Name of the file to delete
	*/
	function deleteLogFile($fileName){
		$fileName = DIR_APPLICATION_BASE.'logs'.DIRECTORY_SEPARATOR."error_{$fileName}.xml";
		if(!unlink($fileName)){
			trigger_error("Unable to delete log file : '$fileName'");
		}
	}
}
?>