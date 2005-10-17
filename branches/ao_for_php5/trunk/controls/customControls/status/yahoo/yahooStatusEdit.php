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
	Class: yahooStatusEdit
		Edits yahoo status.
	
	See Also:
		<ControlBase>
*/
class yahooStatusEdit extends ControlBase{

	/*
   		Function: actionPerform
			Edits yahoo status.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$updateMessage = "";
		$moduleID      = $this->getModuleID($skin->main);
		
		if(isset($_POST["event"]) && $_POST["event"]=='yahooStatusEdit'){
			$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='yahoo_id' AND module_id={$moduleID}";
		
			$recordSet = $skin->main->databaseConnection->Execute($query);
						
			//Check for error, if an error occured then report that error
			if (!$recordSet) {
				trigger_error("Unable to set yahoo id\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			}else{		
				$rows = $recordSet->GetRows();
							
				if(sizeof($rows)==1){
						//Such a entry exists, so execute an update query
						$skin->main->databaseConnection->Execute("UPDATE {$skin->main->databaseTablePrefix}module_settings SET setting_value = \"{$_POST['yahooID']}\" WHERE setting_key='yahoo_id' AND module_id={$moduleID}");
						$updateMessage = 'Yahoo! Id successfully updated!';
					}else{
						//Does not exists such a value, so execute an insert query
						$skin->main->databaseConnection->Execute("INSERT INTO {$skin->main->databaseTablePrefix}module_settings (module_id, setting_key, setting_value) VALUES ({$moduleID}, \"yahoo_id\", \"{$_POST['yahooID']}\")");
						$updateMessage = 'Yahoo! Id successfully added!';
					}
			}
		}
	
		$skin->main->controlVariables["yahooStatusEdit"]['yahooID']        = $this->getYahooID($skin->main, $moduleID);
		$skin->main->controlVariables["yahooStatusEdit"]['moduleId']       = $moduleID;
		$skin->main->controlVariables["yahooStatusEdit"]['updateMessage']  = $updateMessage;
	}
	
	/*	
		Function: getYahooID
			Finds yahoo id for current module.
			
		Returns:
			Yahoo id placed in module_settings table,  empty string otherwise.
			
		Note:
			This function queries module_settings table for yahooId, returns 
		value of setting_value field whose setting_key is 'yahoo_id' and 
		module_id equals to current module id.
	*/
	function getYahooID($main, $moduleID){
		$query =  "SELECT  *  FROM {$main->databaseTablePrefix}module_settings WHERE setting_key='yahoo_id' AND module_id={$moduleID}";
	
		$recordSet = $main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get yahoo id\nreason is : ".$main->databaseConnection->ErrorMsg());
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
		in the following format; DisplayModule:1:editYahooStatus where 1 is the moduleId.
		
		See Also:
			<Main>
	*/
	function getModuleID($main){
		//Get moduleId from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if(sizeof($commands)==3 && $commands[0]=='DisplayModule' && $commands[2]=='editYahooStatus' && $main->checkString('[^0-9]', $commands[1])){
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