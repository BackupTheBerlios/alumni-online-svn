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
Class: editTab
	Edits current tab. Administrative custom control that edits
active tab. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class editTab extends ControlBase{

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
			Overrides actionPerform function of ControlBase class. Controls
		postbacks, bind some variables for code behind and edits active tab.
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
			
		See Also:
			<updateTab>
			<bindTabList>
			<bindTabInfo>
			<Skin>
			<Main>
	*/
	function actionPerform(&$skin, $moduleID){
		$this->main = $skin->main;
		$this->tabID = $this->getTabID();
		
		//Assign initial codeBehind variables
		$skin->main->controlVariables["editTab"] = array(
														'tabnameError'   => false,
														'titleError'     => false,
														'editRolesError' => false,
														'viewRolesError' => false,
														'theme'	         => "",
														'tabList'        => array(),
														'viewUsers'      => array(),
														'notViewUsers'   => array(),
														'editUsers'      => array(),
														'notEditUsers'   => array(),
														'tabInfo'	     => array(
																			'tab_name'    => '',
																			'tab_id'      => '',
																			'is_visible'  => '',
																			'title'       => '',
																			'description' => '',
																			'keywords'    => ''
																		)
														);
		
		if(!$this->canUserEdit()){
			trigger_error("Unauthorized access to edit tab '{$this->tabID}' by user group '{$this->main->userGroup}'");
			$skin->main->selectedTab = $skin->main->getInitialTab();
			$skin->main->revalidate  = TRUE;
			return;
		}
		
		if($this->tabID==NULL){
			return;
		}
		
		if(isset($_POST["event"]) && $_POST["event"]=='editTab'){
			$this->updateTab();
			
			$skin->main->revalidate  = TRUE;
			$_POST["event"] = '';
		}
		
		$this->bindTabList();
		$this->bindTabInfo();
	}
	
	function canUserEdit(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT *  FROM {$this->main->databaseTablePrefix}tabs WHERE  tab_id={$this->tabID} AND administrator_roles LIKE '%;{$this->main->userGroup};%'");

		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to check user right for tab '{$this->tabID}'\nreason is : ".$this->main->databaseConnection->ErrorMsg());
			return false;
		}else{
			return (sizeof($recordSet->GetRows())==1);
		}
	}
	
	/*	
		Function: bindTabInfo
			Gets given tab information and bind them to used in code behind
	*/
	function bindTabInfo(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT *  FROM {$this->main->databaseTablePrefix}tabs WHERE  tab_id={$this->tabID}");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get tab information\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			$this->main->controlVariables["editTab"]["tabInfo"] = $rows[0];
		}
	}
	
	/*	
		Function: bindTabList
			Gets site tab list and bind them to used in code behind
	*/
	function bindTabList(){
		$tabList 	= array();
		$recordSet	= $this->main->databaseConnection->Execute("SELECT *  
														FROM {$this->main->databaseTablePrefix}tabs  
														WHERE  
															parent_id=0  
															AND tab_id>0
															AND (tab_order BETWEEN 0 AND 100000)
														ORDER BY  
															tab_order");
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get component list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{		
			while (!$recordSet->EOF) {
				$tabList[$recordSet->fields["tab_id"]] = $recordSet->fields["tab_name"];
				$recordSet->MoveNext();
			}
		}			
		
		//Assign codeBehind variables
		$this->main->controlVariables["editTab"]["theme"]	     = $this->main->applicationSettings['theme'];
		$this->main->controlVariables["editTab"]["tabList"]      = $tabList;
		$this->main->controlVariables["editTab"]["viewUsers"]    = $this->getViewUsers();
		$this->main->controlVariables["editTab"]["notViewUsers"] = $this->getNotViewUsers();
		$this->main->controlVariables["editTab"]["editUsers"]    = $this->getEditUsers();
		$this->main->controlVariables["editTab"]["notEditUsers"] = $this->getNotEditUsers();	
	}
	
	/*	
		Function: getTabID
			Finds tab id to edit.
			
		Returns:
			TabID given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; EditTab:1 where 1 is the tab id.
		
		See Also:
			<Main>
	*/
	function getTabID(){
		//Get tabd id from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if($commands[0]=='EditTab' && $this->main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Tab id to edit is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Tab id to edit is not supplied!");
			return NULL;
		}
	}
	
	/*	
		Function: updateTab
			Updates given tab information with variables retrieved via post.
			
		Returns:
			True if operation succeeded,; false otherwise.
	*/
	function updateTab(){
		if($this->isReady2Update($skin->main)){
			$isHidden = isset($_POST["ishidden"]) ? 0 : 1;
			
			$authorizedRoles    = ';'.str_replace(',' , ';', $_POST["viewUsersList"]).';';
			$administratorRoles = ';'.str_replace(',' , ';', $_POST["editUsersList"]).';';
			
			$recordSet = $this->main->databaseConnection->Execute("UPDATE {$this->main->databaseTablePrefix}tabs 
															SET
																tab_name = '".addslashes($_POST["tabname"])."', 
																authorized_roles = '$authorizedRoles', 
																administrator_roles = '$administratorRoles', 
																parent_id = ".$_POST["parenttab"].", 
																is_visible = $isHidden, 
																title = '".addslashes($_POST["title"])."', 
																description  ='".addslashes($_POST["description"])."', 
																keywords = '".addslashes($_POST["keywords"])."'
															WHERE
																tab_id = {$this->tabID}");
						
			//Check for error, if an error occured then report that error
			if (!$recordSet) {
				trigger_error("Unable to update tab\nreason is : ".$this->main->databaseConnection->ErrorMsg());
				return false;
			}
			
			return true;
		}
		
		return false;
	}
	
	/*	
		Function: isReady2Update
			Checks whether tab is ready to add. 
			
		Returns:
			True if all required fields filled; false otherwise.
	*/
	function isReady2Update($main){
		$result = true;
		
		if($_POST["tabname"]==""){
			$result = false;
			$main->controlVariables["addTab"]["tabnameError"] = true;
		}
		
		if($_POST["title"]==""){
			$result = false;
			$main->controlVariables["addTab"]["titleError"] = true;
		}
		
		if($_POST["editUsersList"]==""){
			$result = false;
			$main->controlVariables["addTab"]["editRolesError"] = true;
		}
		
		if($_POST["viewUsersList"]==""){
			$result = false;
			$main->controlVariables["addTab"]["viewRolesError"] = true;
		}
		
		return $result;
	}
	
	/*	
		Function: getViewUsers
			Gets list of users that can view tab.
			
		Returns:
			List of users with view right.
	*/
	function getViewUsers(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT authorized_roles FROM {$this->main->databaseTablePrefix}tabs WHERE  tab_id={$this->tabID}");
		
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
			Gets list of users that cannot view tab.
			
		Returns:
			List of users without view right.
	*/
	function getNotViewUsers(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT authorized_roles FROM {$this->main->databaseTablePrefix}tabs WHERE  tab_id={$this->tabID}");
		
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
			Gets list of users that can edit tab.
			
		Returns:
			List of users with edit right.
	*/
	function getEditUsers(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT administrator_roles FROM {$this->main->databaseTablePrefix}tabs WHERE  tab_id={$this->tabID}");
		
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
			Gets list of users that cannot edit tab.
			
		Returns:
			List of users without edit right.
	*/
	function getNotEditUsers(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT administrator_roles FROM {$this->main->databaseTablePrefix}tabs WHERE  tab_id={$this->tabID}");
		
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