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
Class: addUser
	Add new user to the site. Extends ControlBase for skin entegration.
	
	See Also:
		<ControlBase>
*/
class addUser extends ControlBase{

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
		$this->main	   = $skin->main;
		$isFormValid   = true;
		$isUserAdded   = false;
		
		$usernameError = '';
		$nameError     = '';
		$surnameError  = '';
		$passwdError   = '';
		$emailError    = '';
		$groupError    = '';
		
		$username 	   = '';
		$name 	       = '';
		$surname       = '';
		$email 	       = '';
		$group		   = '';
		
		$failMessage   = '';
		
		if(isset($_POST["event"]) && $_POST["event"]=='addUser'){
			if(isset($_POST["username"]) && !$skin->main->checkString('[a-zA-Z0-9]', $_POST["username"])){
				$username = $_POST["username"];
			}else{
				$usernameError = 'Invalid user name! must contains characters and numbers only';
				$isFormValid = false;
			}
			
			if(isset($_POST["name"]) && !$skin->main->checkString('[a-zA-Z0-9.]', $_POST["name"])){
				$name = $_POST["name"];
			}else{
				$nameError = 'Invalid name! must contains characters and numbers only';
				$isFormValid = false;
			}
			
			if(isset($_POST["surname"]) && !$skin->main->checkString('[a-zA-Z0-9.]', $_POST["surname"])){
				$surname = $_POST["surname"];
			}else{
				$surnameError = 'Invalid surname! must contains characters and numbers only';
				$isFormValid = false;
			}
			
			if(isset($_POST["passwd"]) && !$skin->main->checkString('[a-zA-Z0-9.]', $_POST["passwd"])){
				$passwd = md5($_POST["passwd"]);
			}else{
				$passwdError = 'Invalid password! must contains characters, numbers and dot only';
				$isFormValid = false;
			}
			
			if(isset($_POST["email"]) && !$skin->main->checkString('[_a-zA-z0-9\-]+(\.[_a-zA-z0-9\-]+)*\@[_a-zA-z0-9\-]+(\.[a-zA-z]{1,3})+', $_POST["email"])){
				$email = $_POST["email"];
			}else{
				$emailError = 'Invalid email!';
				$isFormValid = false;
			}
			
			if(isset($_POST["group"])){
				$group = $_POST["group"];
			}else{
				$groupError = 'Invalid user group!';
				$isFormValid = false;
			}
			
			if(isset($_POST["isActive"])){
				$isActive = '1';
			}else{
				$isActive = '0';
			}
			
			if($isFormValid){
				$recordSet = $skin->main->databaseConnection->Execute("INSERT INTO {$skin->main->databaseTablePrefix}users 
																	(
																		username,
																		password, 
																		name, 
																		surname, 
																		user_group_id, 
																		email, 
																		is_active
																	)
																	VALUES
																	(
																		".$skin->main->databaseConnection->qstr($username).",
																		".$skin->main->databaseConnection->qstr($passwd).",
																		".$skin->main->databaseConnection->qstr($name).",
																		".$skin->main->databaseConnection->qstr($surname).",
																		$group,
																		".$skin->main->databaseConnection->qstr($email).",
																		$isActive																																																	
																	)");
																	
				//Check for error, if an error occured then report that error
				if (!$recordSet) {
					$isUserAdded = false;
					$failMessage = $skin->main->databaseConnection->ErrorMsg();
				}else{																	
					$skin->main->revalidate  = true;
					$_POST["event"]          = '';
					$isUserAdded             = true;
					
					$this->main->eventHandler->fireEvent("user_new");
				}
			}
		}
		
		//Assign codeBehind variables
		$skin->main->controlVariables["addUser"] = array(
													'groupList'	    =>  $this->getGroupList(),
													'usernameError' => $usernameError,
													'nameError'     => $nameError,
													'surnameError'  => $surnameError,
													'passwdError'   => $passwdError,
													'emailError'    => $emailError,
													'groupError'    => $groupError,
													'isUserAdded'   => $isUserAdded,
													'failMessage'   => $failMessage,
													'postVariables' => array(
																		'username' => htmlspecialchars($username),
																		'name'     => htmlspecialchars($name),
																		'surname'  => htmlspecialchars($surname),
																		'email'    => htmlspecialchars($email),
																		'group'    => htmlspecialchars($group))
													);
	}
	
	function getGroupList(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT * FROM {$this->main->databaseTablePrefix}user_groups");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user group list\nreason is : ".$this->main->databaseConnection->ErrorMsg());
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