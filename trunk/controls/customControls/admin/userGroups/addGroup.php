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
Class: addGroup
	Adds new Group. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class addGroup extends ControlBase{	
	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. 
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
	*/
	function actionPerform(&$skin, $moduleID){
		$error   = '';
		$message = '';
		
		if(isset($_POST["event"]) && $_POST["event"]=='addGroup'){
			if(isset($_POST["group"]) && !$skin->main->checkString('[a-zA-Z0-9]', $_POST["group"])){
				$group = $_POST["group"];
				
				$recordSet = $skin->main->databaseConnection->Execute("INSERT INTO {$skin->main->databaseTablePrefix}user_groups  ( name ) VALUES ('$group')");
																	
				//Check for error, if an error occured then report that error
				if (!$recordSet) {
					$error = $skin->main->databaseConnection->ErrorMsg();
				}else{																	
					$message = "Group '$group' successfully added!";
				}
			}else{
				$error = 'Invalid group name! must contains characters and numbers only';
			}
		}
	
		$skin->main->controlVariables["addGroup"]['error']    = $error;
		$skin->main->controlVariables["addGroup"]['message']  = $message;
		$skin->main->controlVariables["addGroup"]['moduleId'] = $this->getModuleID($skin->main);
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
			if(sizeof($commands)==3 && $commands[0]=='DisplayModule' && $commands[2]=='addGroup' && $main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Module id is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Module id is not supplied!");
			return NULL;
		}
	}
}
?>