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
Class: moveModule
	Moves given module to leftPane. Administrative custom control that moves
given module to desired pane. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class moveModule extends ControlBase{

	/*
		Object: $main
			Main class instance
	*/
	var $main;
	
	/*
		Integer: $moduleId
			Module id to move
	*/
	var $moduleId;

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
		$this->main     = $skin->main;
		$this->moduleId = $this->getModuleId();
		$paneName	    = $this->getPaneName();
		
		if($this->moduleId==NULL || $paneName==NULL || !$this->userCanMove()){
			$skin->main->selectedTab = $skin->main->getInitialTab();
			$skin->main->revalidate  = TRUE;
			$_POST["event"] = '';
			return;
		}
		
		if($paneName!='Up' || $paneName!='Down'){
			$this->main->databaseConnection->Execute("UPDATE {$this->main->databaseTablePrefix}modules 
														SET
															pane_name = '$paneName'
														WHERE
															module_id  = {$this->moduleId}");

			unset($_SERVER['QUERY_STRING']);	
			$skin->main->selectedTab = $this->getTabId();
			$skin->main->revalidate  = TRUE;
		}else{
			//todo implement move module up/down code!!
		}
	}
	
	/*	
		Function: userCanMove
			Checks whether user has right to move given module
	*/
	function userCanMove(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT * FROM {$this->main->databaseTablePrefix}modules WHERE module_id={$this->moduleId} AND (administrator_roles LIKE '%;1;%' OR administrator_roles LIKE '%;{$this->main->userGroup};%')");

		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to check user authentication for module move\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{
			$rows = $recordSet->GetRows();

			return (sizeof($rows)==1);
		}
		
		return false;
	}
	
	function getTabId(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT tab_id FROM {$this->main->databaseTablePrefix}modules WHERE module_id  = {$this->moduleId}");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get tab_id\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			if(sizeof($rows)==1){
				return $rows[0]['tab_id'];
			}
		}
		
		return $this->main->getInitialTab();
	}
	
	/*	
		Function: getModuleId
			Finds module id to move.
			
		Returns:
			ModuleId given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; MoveModule:Left:1 where 1 is the tab id.
	*/
	function getModuleId(){
		//Get module id from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){ 
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if(sizeof($commands)==3 && $commands[0]=='MoveModule' && $this->main->checkString('[^0-9]', $commands[2])){
				return $commands[2];
			}
		}
		
		trigger_error("Module id to move is not supplied!");
		return NULL;
	}
	
	/*	
		Function: getPaneName
			Finds pane to move.
			
		Returns:
			PaneName given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; MoveModule:Left:1 where Left is the pane.
	*/
	function getPaneName(){
		//Get PaneName from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if(sizeof($commands)==3 && $commands[0]=='MoveModule'){
				switch ($commands[1]){
					case 'Left' :
						return 'leftPane';
						break;
				 	case 'Content' :
						return 'contentPane';
						break;
					case 'Right' :
						return 'rightPane';
						break;	
					case 'Up' :
						return 'Up';
						break;
					case 'Down'	:
						return 'Down';
						break;
				}
			}
		}
		
		trigger_error("Tab id to move is not supplied!");
		return NULL;
	}
}
?>