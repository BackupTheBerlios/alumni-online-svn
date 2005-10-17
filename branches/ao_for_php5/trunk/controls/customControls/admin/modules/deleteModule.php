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
Class: deleteModule
	Deletes given module to leftPane. Administrative custom control that deletes
given module. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class deleteModule extends ControlBase{

	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. Extracts
		module id from query string and moves given module to leftPane
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
			
		See Also:
			<Skin>
	*/
	function actionPerform(&$skin, $moduleID){
		$deleteModuleID 	= $this->getModuleID($skin->main);
			
		//Redirect to initial tab if no moduleID provided
		//or user has no delete right	
		if($deleteModuleID==NULL || !$this->userCanDelete($skin->main, $deleteModuleID)){
			$skin->main->selectedTab = $skin->main->getInitialTab();
			$skin->main->revalidate  = TRUE;
			$_POST["event"] = '';
			return;
		}
		
		if(isset($_POST["event"]) && $_POST["event"]=='deleteModule'){
			//If tab deleted, refresh view
			if($this->delete($skin->main, $deleteModuleID)){
				$skin->main->selectedTab = $skin->main->getInitialTab();
				$skin->main->revalidate  = TRUE;
				$_POST["event"] = '';
			}
		}
		
		$skin->main->controlVariables["deleteModule"] = array(
															'moduleID' 		=> $deleteModuleID,
															'moduleTitle'	=> $this->getModuleTitle($skin->main, $deleteModuleID)												
														  );
	}
	
	/*	
		Function: userCanDelete
			Checks whether user has right to delete given module
			
		Parameters:
		
			$main  - Application main class
			$moduleID  - module identifier to delete
	*/
	function userCanDelete($main, $moduleID){
		$recordSet = $main->databaseConnection->Execute("SELECT * FROM {$main->databaseTablePrefix}modules WHERE module_id={$moduleID} AND (administrator_roles LIKE '%;1;%' OR administrator_roles LIKE '%;{$main->userGroup};%')");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to check user authentication for module delete\nreason is : ".$main->databaseConnection->ErrorMsg());
		}else{
			$rows = $recordSet->GetRows();

			return (sizeof($rows)==1);
		}
		
		return false;
	}
	
	/*	
		Function: delete
			Deletes given module from the site.
		
		Parameters:
			$main	- Alumni-Online's main class instance
			$tabID	- Id of the tab to delete
			
		Returns:
			True if delete succeed; false otherwise.
	*/
	function delete($main, $moduleID){
		$recordSet = $main->databaseConnection->Execute("DELETE FROM {$main->databaseTablePrefix}modules WHERE module_id =$moduleID");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to delete module\nreason is : ".$main->databaseConnection->ErrorMsg());
			return false;
		}
			
		return true;
	}
	
	function getModuleTitle($main, $moduleID){
		$recordSet = $main->databaseConnection->Execute("SELECT module_title  FROM {$main->databaseTablePrefix}modules WHERE module_id=$moduleID");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get module title\nreason is : ".$main->databaseConnection->ErrorMsg());
			return '';
		}
			
		$rows = $recordSet->GetRows();
		if(sizeof($rows)==1){
			return $rows[0]["module_title"];
		}
		
		return '';
	}
	
	/*	
		Function: getModuleID
			Finds module id to delete.
			
		Returns:
			ModuleID given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; DeleteModule:1 where 1 is the module id.

	*/
	function getModuleID($main){
		//Get module id from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if($commands[0]=='DeleteModule' && $main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Module id to delete is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Module id to delete is not supplied!");
			return NULL;
		}
	}
}
?>