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
Class: addModule
	Adds new module. Administrative custom control that adds new module to the site.
Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>	
*/
class addModule extends ControlBase{
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
		Integer: $ModuleID
			Id of the module that is being add to the tab
	*/
	var $ModuleID;
	
	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. Controls
		postbacks, bind some variables for code behind and adds new module.
			
		Parameters:
		
			$skin  - Application skin class$moduleID  - Current module identifier
			
		See Also:
			<Skin>
			<add>
			<Main>
	*/
	function actionPerform(&$skin, $moduleID){
		$this->main 	= $skin->main;
		$this->tabID 	= $this->getTabID();
		$this->ModuleID = $this->getModuleID();
		$displayForm 	= true;
		
		//Assign codeBehind variables
		$skin->main->controlVariables["addModule"] = array(
													'theme'			=> $skin->main->applicationSettings['theme'],
													'tabId' 		=> $this->tabID,
													'moduleId'		=> $this->ModuleID,
													'iconList' 		=> array(),
													'containerList' => $skin->main->getContainerList(),
													'groupList'     => $this->getGroupList(),
													'titleError'    => false,
													'editRolesError'=> false,
													'viewRolesError'=> false);
				
		if($this->tabID==NULL || $this->ModuleID==NULL){
			return;
		}
		
		if(isset($_POST["event"]) && $_POST["event"]=='addModule'){
			$displayForm = !$this->add();
		}
		
		$skin->main->controlVariables["addModule"]["displayForm"] = $displayForm;	
	}
	
	/*	
		Function: add
			Adds new module to the site.Parameters retrives via post method
	*/
	function add(){
		if($this->isReady2Add()){
			$showTitle 		    = isset($_POST["showTitle"]) ? 1 : 0;
			$displayAllTabs     = isset($_POST["allTabs"]) ? 1 : 0;
			$align			    = (isset($_POST["align"]) && ereg('/(left|center|right)', $_POST["align"])) ? '' : $_POST["align"];
			$panel			    = (isset($_POST["panel"]) && ereg('/(leftPane|contentPane|rightPane)', $_POST["panel"])) ? 'contentPane' : $_POST["panel"];
			$icon			    = (isset($_POST["icon"]) && $_POST["icon"]!="") ? "'".addslashes($_POST["icon"])."'" : "NULL";
			$authorizedRoles    = ';'.str_replace(',' , ';', $_POST["viewUsersList"]).';';
			$administratorRoles = ';'.str_replace(',' , ';', $_POST["editUsersList"]).';';
	
			$this->main->databaseConnection->StartTrans();
			
			$recordSet = $this->main->databaseConnection->Execute("INSERT INTO {$this->main->databaseTablePrefix}modules
																	(
																		tab_id,
																		module_definition_id,
																		module_order,
																		authorized_roles,
																		administrator_roles,
																		pane_name,
																		module_title,
																		module_icon,
																		alignment,
																		can_show_title,
																		display_all_tabs
																	)
																	VALUES
																	(
																		{$this->tabID},
																		{$this->ModuleID},
																		".$this->findModuleOrder($panel).",
																		'$authorizedRoles',
																		'$administratorRoles',
																		'$panel',
																		'".addslashes($_POST["title"])."',
																		$icon,
																		'$align',
																		$showTitle,
																		$displayAllTabs
																	)");
																	
			
			//Check for error, if an error occured then report that error
			if (!$recordSet) {
				trigger_error("Unable to add module\nreason is : ".$this->main->databaseConnection->ErrorMsg());
				$this->main->databaseConnection->CompleteTrans();
				return false;
			}
			
			if($_POST["skin"]!=""){			
				$this->main->databaseConnection->Execute("INSERT INTO {$this->main->databaseTablePrefix}module_settings
															(
																module_id,
																setting_key,
																setting_value
															)
															VALUES
															(
																".$this->main->databaseConnection->Insert_ID().",
																'container',
																'{$_POST['skin']}'
															)");
			}
			
			$this->main->databaseConnection->CompleteTrans();
			
			return true;
		}else{
			return false;
		}
	}
	
	function findModuleOrder($panel){
		$recordSet = $this->main->databaseConnection->Execute("SELECT (MAX(module_order) + 1) AS next_order FROM {$this->main->databaseTablePrefix}modules 
																WHERE 
																	tab_id={$this->tabID} 
																AND 
																	pane_name='$panel'
																GROUP BY 
																	tab_id");
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get next module order\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){
				return $rows[0]['next_order'];
			}
		}
		
		return 0;														
	}
	
	/*	
		Function: isReady2Add
			Checks whether module is ready to add. 
			
		Returns:
			True if all required fields filled; false otherwise.
	*/
	function isReady2Add(){
		$result = true;
		
		if($_POST["title"]==""){
			$result = false;
			$this->main->controlVariables["addModule"]["titleError"] = true;
		}
		
		if($_POST["editUsersList"]==""){
			$result = false;
			$this->main->controlVariables["addModule"]["editRolesError"] = true;
		}
		
		if($_POST["viewUsersList"]==""){
			$result = false;
			$this->main->controlVariables["addModule"]["viewRolesError"] = true;
		}
		
		return $result;
	}
	
	/*	
		Function: getModuleID
			Finds module id to add.
			
		Returns:
			moduleID given via post,  NULL otherwise.
	*/
	function getModuleID(){
		return isset($_POST["moduleID"]) ? $_POST["moduleID"] : NULL;
	}
	
	/*	
		Function: getTabID
			Finds tab id to add module.
			
		Returns:
			TabID given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; AddModule:1 where 1 is the tab id.
		
		See Also:
			<Main>
	*/
	function getTabID(){
		//Get tab id from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			//Check queryString. 
			//Must start with 'AddModule'
			//then ':'
			//finally tabId that must be a number
			if($commands[0]=='AddModule' && sizeof($commands)==2 && $this->main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Tab id to add module is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Tab id to add module is not supplied!");
			return NULL;
		}
	}
	
	/*	
		Function: getGroupList
			Gets user group list.
			
		Returns:
			Avaliable user groups.
			
		See Also:
			<Main>
	*/
	function getGroupList(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT *  FROM {$this->main->databaseTablePrefix}user_groups WHERE user_group_id<>1");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user group list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}
			
		$rows = $recordSet->GetRows();
		return $rows;
	}
}
?>