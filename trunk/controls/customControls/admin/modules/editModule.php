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
Class: editModule
	Adds new module. Administrative custom control that adds new module to the site.
Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>	
*/
class editModule extends ControlBase{
	
	/*
		Object: $main
			Main class instance
	*/
	var $main;
		
	/*
		Integer: $moduleID
			Id of the module that is being edit
	*/
	var $moduleID;
	
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
		$this->moduleID = $this->getModuleID(); 
		
		//Assign codeBehind variables
		$skin->main->controlVariables["editModule"] = array(
													'theme'			=> $skin->main->applicationSettings['theme'],
													'moduleId'		=> $this->moduleID,
													'iconList' 		=> $this->getIconList(),
													'containerList' => $skin->main->getContainerList(),
													'titleError'    => false,
													'editRolesError'=> false,
													'viewRolesError'=> false
													);
				
		if($this->moduleID==NULL  || !$this->userCanEdit()){
			$skin->main->selectedTab = $skin->main->getInitialTab();
			$skin->main->revalidate  = TRUE;
			$_POST["event"] = '';
			return;
		}
		
		if(isset($_POST["event"]) && $_POST["event"]=='editModule'){
			$this->updateModule();
		}
		
		$this->bindModuleSettings();
	}
	
	function userCanEdit(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT * FROM {$this->main->databaseTablePrefix}modules WHERE module_id={$this->moduleID} AND (administrator_roles LIKE '%;1;%' OR administrator_roles LIKE '%;{$this->main->userGroup};%')");

		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to check user authentication for module edit\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{
			$rows = $recordSet->GetRows();

			return (sizeof($rows)==1);
		}
		
		return false;
	}
	
	function getIconList(){
		return array();
	}
	
	function bindModuleSettings(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT *  FROM {$this->main->databaseTablePrefix}modules WHERE module_id={$this->moduleID}");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get module settings\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}
			
		$rows = $recordSet->GetRows();
		
		//Assign codeBehind variables
		$this->main->controlVariables["editModule"]["moduleInfo"]   =  $rows[0];		
		$this->main->controlVariables["editModule"]["viewUsers"]    = $this->getViewUsers();
		$this->main->controlVariables["editModule"]["notViewUsers"] = $this->getNotViewUsers();
		$this->main->controlVariables["editModule"]["editUsers"]    = $this->getEditUsers();
		$this->main->controlVariables["editModule"]["notEditUsers"] = $this->getNotEditUsers();
		$this->main->controlVariables["editModule"]["moduleContainer"] = $this->getModuleContainer();
	}
	
	/*	
		Function: getModuleID
			Finds module id to edit.
			
		Returns:
			ModuleID given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; EditModule:1 where 1 is the module id.

	*/
	function getModuleID(){
		//Get module id from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if($commands[0]=='EditModule' && $this->main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Module id to edit is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Module id to edit is not supplied!");
			return NULL;
		}
	}
	
	/*	
		Function: updateModule
			Updates given module information with variables retrieved via post.
			
		Returns:
			True if operation succeeded,; false otherwise.
	*/
	function updateModule(){
		if($this->isReady2Update()){
			$showTitle 		    = isset($_POST["showTitle"]) ? 1 : 0;
			$displayAllTabs     = isset($_POST["allTabs"]) ? 1 : 0;
			$align			    = (isset($_POST["align"]) && ereg('/(left|center|right)', $_POST["align"])) ? '' : $_POST["align"];
			$panel			    = (isset($_POST["panel"]) && ereg('/(leftPane|contentPane|rightPane)', $_POST["panel"])) ? 'contentPane' : $_POST["panel"];
			$icon			    = (isset($_POST["icon"]) && $_POST["icon"]!="") ? "'".addslashes($_POST["icon"])."'" : "NULL";
			$authorizedRoles    = ';'.str_replace(',' , ';', $_POST["viewUsersList"]).';';
			$administratorRoles = ';'.str_replace(',' , ';', $_POST["editUsersList"]).';';
			
			$this->main->databaseConnection->StartTrans();
			$recordSet = $this->main->databaseConnection->Execute("UPDATE {$this->main->databaseTablePrefix}modules 
																	SET
																				authorized_roles = '$authorizedRoles',
																				administrator_roles = '$administratorRoles',
																				pane_name = '$panel',
																				module_title = '".addslashes($_POST["title"])."',
																				module_icon = $icon,
																				alignment = '$align',
																				can_show_title = $showTitle,
																				display_all_tabs = $displayAllTabs																		
																	WHERE
																		module_id = {$this->moduleID}");
						
			//Check for error, if an error occured then report that error
			if (!$recordSet) {
				trigger_error("Unable to update module\nreason is : ".$this->main->databaseConnection->ErrorMsg());
				$this->main->databaseConnection->CompleteTrans();
				return false;
			}else{
				if($_POST["skin"]==""){
					$this->main->databaseConnection->Execute("DELETE FROM {$this->main->databaseTablePrefix}module_settings
																WHERE
																	module_id = {$this->moduleID}
																AND
																	setting_key = 'container'");
				}else{
					$this->main->databaseConnection->Replace($this->main->databaseTablePrefix.'module_settings', 
															array(
																'module_id'     => $this->moduleID,
																'setting_key'   => 'container',
																'setting_value' => $_POST['skin']
																),
															array(
																'module_id',
																'setting_key'
																),
															$autoquote = true);
				}
			}
			
			$this->main->databaseConnection->CompleteTrans();
			return true;
		}
		
		return false;
	}
	
	/*	
		Function: isReady2Update
			Checks whether module is ready to update. 
			
		Returns:
			True if all required fields filled; false otherwise.
	*/
	function isReady2Update(){
		$result = true;
		
		if($_POST["title"]==""){
			$result = false;
			$this->main->controlVariables["editModule"]["titleError"] = true;
		}
		
		if($_POST["editUsersList"]==""){
			$result = false;
			$this->main->controlVariables["editModule"]["editRolesError"] = true;
		}
		
		if($_POST["viewUsersList"]==""){
			$result = false;
			$this->main->controlVariables["editModule"]["viewRolesError"] = true;
		}
		
		return $result;
	}
	
	/*
   		Function: getModuleContainer
			Finds container from given module id
			
		Returns:
			Container name for current module
   */
   function getModuleContainer(){
	$result = "";
	$recordSet = $this->main->databaseConnection->Execute("SELECT  *  
											FROM {$this->main->databaseTablePrefix}module_settings
											WHERE 
												module_id  = {$this->moduleID}
												AND setting_key= 'container'");
												
	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get module container\nreason is : ".$this->main->databaseConnection->ErrorMsg());
	}else{
		$rows = $recordSet->GetRows();
		if(sizeof($rows)==1){
			$result = $rows[0]["setting_value"];
		}
	}
											
   	return $result;
   }
	
	/*	
		Function: getViewUsers
			Gets list of users that can view module.
			
		Returns:
			List of users with view right.
	*/
	function getViewUsers(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT authorized_roles FROM {$this->main->databaseTablePrefix}modules WHERE  module_id ={$this->moduleID}");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get view users list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
			return array();
		}else{		
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){
				$users = str_replace(';', ',', trim($rows[0]["authorized_roles"], ';'));
				
				$recordSet2 = $this->main->databaseConnection->Execute("SELECT * FROM {$this->main->databaseTablePrefix}user_groups WHERE user_group_id IN ($users)");
				
				//Check for error, if an error occured then report that error
				if (!$recordSet2) {
					trigger_error("Unable to get view users list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
					return array();
				}else{		
					return $recordSet2->GetRows();
				}
			}else{
				return array();
			}
		}
	}
	
	/*	
		Function: getNotViewUsers
			Gets list of users that cannot view module.
			
		Returns:
			List of users without view right.
	*/
	function getNotViewUsers(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT authorized_roles FROM {$this->main->databaseTablePrefix}modules WHERE  module_id ={$this->moduleID}");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get not view users list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
			return array();
		}else{		
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){
				$users = str_replace(';', ',', trim($rows[0]["authorized_roles"], ';'));
				
				$recordSet2 = $this->main->databaseConnection->Execute("SELECT * FROM {$this->main->databaseTablePrefix}user_groups WHERE user_group_id NOT IN ($users)");
				
				//Check for error, if an error occured then report that error
				if (!$recordSet2) {
					trigger_error("Unable to get not view users list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
					return array();
				}else{		
					return $recordSet2->GetRows();
				}
			}else{
				return array();
			}
		}
	}
	
	/*	
		Function: getEditUsers
			Gets list of users that can edit module.
			
		Returns:
			List of users with edit right.
	*/
	function getEditUsers(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT administrator_roles FROM {$this->main->databaseTablePrefix}modules WHERE  module_id ={$this->moduleID}");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get edit users list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
			return array();
		}else{		
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){
				$users = str_replace(';', ',', trim($rows[0]["administrator_roles"], ';'));
				
				$recordSet2 = $this->main->databaseConnection->Execute("SELECT * FROM {$this->main->databaseTablePrefix}user_groups WHERE user_group_id IN ($users)");
				
				//Check for error, if an error occured then report that error
				if (!$recordSet2) {
					trigger_error("Unable to get edit users list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
					return array();
				}else{		
					return $recordSet2->GetRows();
				}
			}else{
				return array();
			}
		}
	}
	
	/*	
		Function: getNotEditUsers
			Gets list of users that cannot edit module.
			
		Returns:
			List of users without edit right.
	*/
	function getNotEditUsers(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT administrator_roles FROM {$this->main->databaseTablePrefix}modules WHERE  module_id ={$this->moduleID}");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get not edit users list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
			return array();
		}else{		
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){
				$users = str_replace(';', ',', trim($rows[0]["administrator_roles"], ';'));
				
				$recordSet2 = $this->main->databaseConnection->Execute("SELECT * FROM {$this->main->databaseTablePrefix}user_groups WHERE user_group_id NOT IN ($users)");
				
				//Check for error, if an error occured then report that error
				if (!$recordSet2) {
					trigger_error("Unable to get not edit users list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
					return array();
				}else{		
					return $recordSet2->GetRows();
				}
			}else{
				return array();
			}
		}
	}
}
?>