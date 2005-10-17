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

require_once(DIR_CONTROLS.'controls.Mailer.php');

/*
Class: sendMessage
	Send a message to user. Extends ControlBase for skin entegration.
	
	See Also:
		<ControlBase>
		<Mailer>
*/
class sendMessage extends ControlBase{
	
	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. 
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
	*/
	function actionPerform(&$skin, $moduleID){
		$recordSet = $skin->main->databaseConnection->Execute("SELECT * FROM {$skin->main->databaseTablePrefix}users");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user list\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			$skin->main->controlVariables["sendMessage"]['userList']  = $rows;
			$skin->main->controlVariables["sendMessage"]['moduleId']  = $this->getModuleID($skin->main);
		}
		
		$skin->main->controlVariables["sendMessage"]['errorInfo']  = "";
		$skin->main->controlVariables["sendMessage"]['succeed']    = false;
		if(isset($_POST["event"]) && $_POST["event"]=='sendMessage'){
			$mailer = new Mailer($skin->main);
			for($i=0;$i<sizeof($_POST["users"]);$i++){
				$mailer->addUserAddress($_POST["users"][$i]);
			}
			
			$mailer->Subject = $_POST["subject"];
			$mailer->Body    = $_POST["content"];
			$mailer->Send();
			
			$skin->main->controlVariables["sendMessage"]['errorInfo']  = $mailer->ErrorInfo;
			$skin->main->controlVariables["sendMessage"]['succeed']    = $mailer->ErrorInfo=="";
		}
	}
	
	/*	
		Function: getModuleID
			Finds moduleId.
			
		Returns:
			ModuleId given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; DisplayModule:1:message2Group where 1 is the moduleId.
		
		See Also:
			<Main>
	*/
	function getModuleID($main){
		//Get moduleId from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if(sizeof($commands)==3 && $commands[0]=='DisplayModule' && $commands[2]=='message2Users' && $main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Module id is not supplied! Query string is '".$_SERVER['QUERY_STRING']."'");
				return NULL;
			}
		}else{
			trigger_error("Module id is not supplied! Query string is '".$_SERVER['QUERY_STRING']."'");
			return NULL;
		}
	}
}
?>