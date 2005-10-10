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

/*
Class: siteSettings
	Edits web site settings. Administrative custom control that edits
web site settings. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class siteSettings extends ControlBase{

	/*
		Object: $main
			Main class instance
	*/
	var $main;
	
	/*
		Integer: $tabID
			Tab to add module
	*/
	var $tabID;
	
	
	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. 
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
	*/
	function actionPerform(&$skin, $moduleID){
		$titleError     = '';
		$emailError     = '';
		$skinError      = '';
		$containerError = '';
	
		if(isset($_POST["event"]) && $_POST["event"]=='siteSettings'){
			
			if(isset($_POST["siteTitle"]) && $_POST["siteTitle"]==''){
				$titleError = "Web site title can not be empty!";
			}else{
				$title = $_POST["siteTitle"];
				$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}configuration 
														  SET config_value='$title'
														  WHERE config_key='title'");
			}
			
			if(isset($_POST["siteEmail"]) && $_POST["siteEmail"]==''){
				$emailError = "Web site email can not be empty!";
			}else{
				$email = $_POST["siteEmail"];
				$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}configuration 
														  SET config_value='$email'
														  WHERE config_key='email'");
			}
			
			if(isset($_POST["siteSkin"]) && $_POST["siteSkin"]==''){
				$skinError = "Web site skin can not be empty!";
			}else{
				$siteSkin = $_POST["siteSkin"];
				$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}configuration 
														  SET config_value='$siteSkin'
														  WHERE config_key='theme'");
			}
			
			if(isset($_POST["siteContainer"]) && $_POST["siteContainer"]==''){
				$containerError = "Web site container can not be empty!";
			}else{
				$container = $_POST["siteContainer"];
				$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}configuration 
														  SET config_value='$container'
														  WHERE config_key='container'");
			}
			
			if(isset($_POST["showVersion"]) && $_POST["showVersion"]!=''){
				$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}configuration 
														  SET config_value='1'
														  WHERE config_key='showVersion'");
			}else{
				$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}configuration 
														  SET config_value='0'
														  WHERE config_key='showVersion'");
			}
			
			$skin->main->revalidate  = TRUE;
			$_POST["event"] = '';
		}
		
		$recordSet = $skin->main->databaseConnection->Execute("SELECT * FROM {$skin->main->databaseTablePrefix}configuration");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get site settings\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			
			for($i=0;$i<sizeof($rows);$i++){
				//Assign codeBehind variables
				$skin->main->controlVariables["siteSettings"][$rows[$i]["config_key"]] = $rows[$i]["config_value"];
			}
			
			$skin->main->controlVariables["siteSettings"]['skinList']       = $skin->main->getSkinList();
			$skin->main->controlVariables["siteSettings"]['containerList']  = $skin->main->getContainerList();
			$skin->main->controlVariables["siteSettings"]['tabId']          = $skin->main->selectedTab;
			$skin->main->controlVariables["siteSettings"]['titleError']     = $titleError;
			$skin->main->controlVariables["siteSettings"]['emailError']     = $emailError;
			$skin->main->controlVariables["siteSettings"]['skinError']      = $skinError;
			$skin->main->controlVariables["siteSettings"]['containerError'] = $containerError;
		}
	}
}
?>