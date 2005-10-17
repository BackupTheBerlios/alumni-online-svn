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
	Class: icqStatusEdit
		Edits icq status.
	
	See Also:
		<ControlBase>
*/
class icqStatusEdit extends ControlBase{

	/*
   		Function: actionPerform
			Edits icq status.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$updateMessage = "";
		$moduleID      = $this->getModuleID($skin->main);
		
		if(isset($_POST["event"]) && $_POST["event"]=='icqStatusEdit'){
			$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='icq_number' AND module_id={$moduleID}";
		
			$recordSet = $skin->main->databaseConnection->Execute($query);
						
			//Check for error, if an error occured then report that error
			if (!$recordSet) {
				trigger_error("Unable to set icq number\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			}else{		
				$rows = $recordSet->GetRows();
							
				if(sizeof($rows)==1){
						//Such a entry exists, so execute an update query
						$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}module_settings SET setting_value = \"{$_POST['icqNumber']}\" WHERE setting_key='icq_number' AND module_id={$moduleID}");
						$updateMessage = 'ICQ number successfully updated!';
					}else{
						//Does not exists such a value, so execute an insert query
						$skin->main->databaseConnection->Execute("INSERT INTO {$skin->main->databaseTablePrefix}module_settings (module_id, setting_key, setting_value) VALUES ({$moduleID}, \"icq_number\", \"{$_POST['icqNumber']}\")");
						$updateMessage = 'ICQ number successfully added!';
					}
			}
		}
	
		$skin->main->controlVariables["icqStatusEdit"]['icqNumber']       = $this->getIcqNumber($skin->main, $moduleID);
		$skin->main->controlVariables["icqStatusEdit"]['moduleId']        = $moduleID;
		$skin->main->controlVariables["icqStatusEdit"]['updateMessage']   = $updateMessage;
	}
		
	/*	
		Function: getIcqNumber
			Finds icq number for current module.
			
		Returns:
			IcqNumber placed in module_settings table,  empty string otherwise.
			
		Note:
			This function queries module_settings table for IcqNumber, returns 
		value of setting_value field whose setting_key is 'icq_number' and 
		module_id equals to current module id.
	*/
	function getIcqNumber($main, $moduleID){
		$query =  "SELECT  *  FROM {$main->databaseTablePrefix}module_settings WHERE setting_key='icq_number' AND module_id={$moduleID}";
	
		$recordSet = $main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get icq number\nreason is : ".$main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
						
			if(sizeof($rows)==1){
				return $rows[0]['setting_value'];
			}
		}
		
		return "";
	}
	
	/*	
		Function: getModuleID
			Finds moduleId.
			
		Returns:
			ModuleId given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; DisplayModule:1:editIcqStatus where 1 is the moduleId.
		
		See Also:
			<Main>
	*/
	function getModuleID($main){
		//Get moduleId from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if(sizeof($commands)==3 && $commands[0]=='DisplayModule' && $commands[2]=='editIcqStatus' && $main->checkString('[^0-9]', $commands[1])){
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