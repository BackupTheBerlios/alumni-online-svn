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
Class: userInfo
	Displays given user's information. Extends ControlBase for skin entegration.
	
	See Also:
		<ControlBase>
*/
class userInfo extends ControlBase{

	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. 
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
	*/
	function actionPerform(&$skin, $moduleID){
		$username  = $this->getUsername($skin->main);
		
		$isFormValid   = true;
		
		$name 	       = '';
		$surname       = '';
		$email 	       = '';
		$group		   = '';
		$passwd		   = '';		
		
		if(isset($_POST["event"]) && $_POST["event"]=='userInfo'){
			if(isset($_POST["name"]) && !$skin->main->checkString('[a-zA-Z0-9.]', $_POST["name"])){
				$name = $_POST["name"];
			}else{
				$isFormValid = false;
			}
			
			if(isset($_POST["surname"]) && !$skin->main->checkString('[a-zA-Z0-9.]', $_POST["surname"])){
				$surname = $_POST["surname"];
			}else{
				$isFormValid = false;
			}
			
			if(isset($_POST["passwd"]) && !$skin->main->checkString('[a-zA-Z0-9.]', $_POST["passwd"])){
				$passwd = md5($_POST["passwd"]);
			}
			
			if(isset($_POST["email"]) && !$skin->main->checkString('[_a-zA-z0-9\-]+(\.[_a-zA-z0-9\-]+)*\@[_a-zA-z0-9\-]+(\.[a-zA-z]{1,3})+', $_POST["email"])){
				$email = $_POST["email"];
			}else{
				$isFormValid = false;
			}
			
			if(isset($_POST["group"])){
				$group = $_POST["group"];
			}else{
				$isFormValid = false;
			}
			
			if(isset($_POST["isActive"])){
				$isActive = '1';
			}else{
				$isActive = '0';
			}
			
			if($isFormValid){
				$query = "SELECT * FROM {$skin->main->databaseTablePrefix}users WHERE username = ".$skin->main->databaseConnection->qstr($username);
				$recordSet = $skin->main->databaseConnection->Execute($query);

				$records2Update = array(); 
				$records2Update["name"]          = $name;
				$records2Update["surname"]       = $surname;
				
				if($passwd!=''){
					$records2Update["password"]  = $passwd;
				}
				
				$records2Update["user_group_id"] = $group;
				$records2Update["email"]         = $email;
				$records2Update["is_active"]     = $isActive;
				
				$updateSQL  = $skin->main->databaseConnection->GetUpdateSQL($recordSet, $records2Update);
				if($updateSQL!=""){
					$skin->main->databaseConnection->Execute($updateSQL);
					if($recordSet->Fields("is_active")!=$isActive){
						if($isActive==1){
							$skin->main->eventHandler->fireEvent("user_activate", $username);
						}else{
							$skin->main->eventHandler->fireEvent("user_deactivate", $username);
						}
					}
				}
			}
		}
		
		$recordSet = $skin->main->databaseConnection->Execute("SELECT * FROM {$skin->main->databaseTablePrefix}users WHERE username='$username'");
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			if(sizeof($rows)==1){
				$skin->main->controlVariables["userInfo"] =  array(
																'groupList' =>  $this->getGroupList($skin->main),
																'username'  => htmlspecialchars($username),
																'name'      => htmlspecialchars($rows[0]["name"]),
																'surname'   => htmlspecialchars($rows[0]["surname"]),
																'group'     => $rows[0]["user_group_id"],
																'email'     => htmlspecialchars($rows[0]["email"]),
																'icq'       => htmlspecialchars($rows[0]["icq"]),
																'msn'       => htmlspecialchars($rows[0]["msn"]),
																'yahoo'     => htmlspecialchars($rows[0]["yahoo"]),
																'isActive'  => htmlspecialchars($rows[0]["is_active"])
															  );
			}else{
				$skin->main->controlVariables["userInfo"] =  array(
																'groupList' =>  $this->getGroupList($skin->main),
																'username'  => htmlspecialchars($username),
																'name'      => '',
																'surname'   => '',
																'group'     => '',
																'email'     => '',
																'icq'       => '',
																'msn'       => '',
																'yahoo'     => '',
																'isActive'  => ''
															  );
			}
		}
	}
	
	/*	
		Function: getUsername
			Finds username to display information.
			
		Returns:
			Username given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; UserInfo:fatih where fatih is the username.
		
		See Also:
			<Main>
	*/
	function getUsername($main){
		//Get username from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){ 
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if($commands[0]=='UserInfo' && $main->checkString('[^a-zA-Z0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Username to display information is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Username to display information is not supplied!");
			return NULL;
		}
	}
	
	function getGroupList($main){
		$recordSet = $main->databaseConnection->Execute("SELECT * FROM {$main->databaseTablePrefix}user_groups");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user group list\nreason is : ".$main->databaseConnection->ErrorMsg());
			return array();
		}else{
			$result = array();
			while (!$recordSet->EOF) {
				$result[$recordSet->fields["user_group_id"]] = $recordSet->fields["name"];
				$recordSet->MoveNext();
			}
			
			return $result;
		}
	}
}
?>