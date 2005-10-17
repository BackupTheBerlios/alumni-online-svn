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
	Class: msnStatusEdit
		Edits msn status.
	
	See Also:
		<ControlBase>
*/
class msnStatusEdit extends ControlBase{

	/*
   		Function: actionPerform
			Edits msn status.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$updateMessage = "";
		$moduleID      = $this->getModuleID($skin->main);
		
		if(isset($_POST["event"]) && $_POST["event"]=='msnStatusEdit'){
			$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='msn_id' AND module_id={$moduleID}";
		
			$recordSet = $skin->main->databaseConnection->Execute($query);
						
			//Check for error, if an error occured then report that error
			if (!$recordSet) {
				trigger_error("Unable to set msn id\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			}else{		
				$rows = $recordSet->GetRows();
							
				if(sizeof($rows)==1){
						//Such a entry exists, so execute an update query
						$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}module_settings SET setting_value = \"{$_POST['msnID']}\" WHERE setting_key='yahoo_id' AND module_id={$moduleID}");
						$updateMessage = 'Msn Id successfully updated!';
					}else{
						//Does not exists such a value, so execute an insert query
						$skin->main->databaseConnection->Execute("INSERT INTO {$skin->main->databaseTablePrefix}module_settings (module_id, setting_key, setting_value) VALUES ({$moduleID}, \"msn_id\", \"{$_POST['msnID']}\")");
						$updateMessage = 'Msn Id successfully added!';
					}
			}
		}
	
		$skin->main->controlVariables["msnStatusEdit"]['msnID']          = $this->getMsnID($skin->main, $moduleID);
		$skin->main->controlVariables["msnStatusEdit"]['moduleId']       = $moduleID;
		$skin->main->controlVariables["msnStatusEdit"]['updateMessage']  = $updateMessage;
	}
	
	/*	
		Function: getMsnID
			Finds msn id for current module.
			
		Returns:
			Msn id placed in module_settings table,  empty string otherwise.
			
		Note:
			This function queries module_settings table for msnId, returns 
		value of setting_value field whose setting_key is 'msn_id' and 
		module_id equals to current module id.
	*/
	function getMsnID($main, $moduleID){
		$query =  "SELECT  *  FROM {$main->databaseTablePrefix}module_settings WHERE setting_key='msn_id' AND module_id={$moduleID}";
	
		$recordSet = $main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get msn id\nreason is : ".$main->databaseConnection->ErrorMsg());
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
		in the following format; DisplayModule:1:editMsnStatus where 1 is the moduleId.
		
		See Also:
			<Main>
	*/
	function getModuleID($main){
		//Get moduleId from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if(sizeof($commands)==3 && $commands[0]=='DisplayModule' && $commands[2]=='editMsnStatus' && $main->checkString('[^0-9]', $commands[1])){
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