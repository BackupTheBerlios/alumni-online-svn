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
Class: deleteGroup
	Adds new role. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class deleteGroup extends ControlBase{

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
		$groupID 		= $this->getGroupID($skin->main);
		
		if($groupID==NULL){
			return;
		}
		
		if(isset($_POST["event"]) && $_POST["event"]=='deleteGroup'){
			//If tab deleted, refresh view
			if($this->delete($skin->main, $groupID)){
				$skin->main->selectedTab = $skin->main->getInitialTab();
				$skin->main->revalidate  = TRUE;
				$_POST["event"] = '';
			}
		}
		
		$skin->main->controlVariables["deleteGroup"] = array(
																'groupID'   => $groupID,
																'groupName' => $this->getGroupName($skin->main, $groupID)
															);
	}
	
	/*	
		Function: getGroupID
			Finds group to delete.
			
		Returns:
			Username given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; DeleteUser:fatih where fatih is the username.
		
		See Also:
			<Main>
	*/
	function getGroupID($main){
		//Get username from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if($commands[0]=='DeleteGroup' && $main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Group id to delete is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Group id to delete is not supplied!");
			return NULL;
		}
	}
	
	function getGroupName($main, $groupID){
		$recordSet = $main->databaseConnection->Execute("SELECT name  FROM {$main->databaseTablePrefix}user_groups WHERE user_group_id=$groupID");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get group name\nreason is : ".$main->databaseConnection->ErrorMsg());
			return '';
		}
			
		$rows = $recordSet->GetRows();
		if(sizeof($rows)==1){
			return $rows[0]["name"];
		}
		
		return '';
	}
	
	/*	
		Function: delete
			Deletes given group from the site.
		
		Parameters:
			$main	   - Alumni-Online's main class instance
			$groupID   - Id of the group to delete
			
		Returns:
			True if delete succeed; false otherwise.
	*/
	function delete($main, $groupID){
		$recordSet = $main->databaseConnection->Execute("DELETE FROM {$main->databaseTablePrefix}user_groups WHERE user_group_id=$groupID");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to delete group\nreason is : ".$main->databaseConnection->ErrorMsg());
			return false;
		}
			
		return true;
	}
}
?>