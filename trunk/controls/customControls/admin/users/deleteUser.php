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
Class: deleteUser
	Deletes given user to the site. Extends ControlBase for skin entegration.
	
	See Also:
		<ControlBase>
*/
class deleteUser extends ControlBase{

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
		$username 		= $this->getUsername($skin->main);
		
		if($username==NULL){
			return;
		}
		
		if(isset($_POST["event"]) && $_POST["event"]=='deleteUser'){
			//If tab deleted, refresh view
			if($this->delete($skin->main, $username)){
				$skin->main->selectedTab = $skin->main->getInitialTab();
				$skin->main->revalidate  = TRUE;
				$_POST["event"] = '';
			}
		}
		
		$skin->main->controlVariables["deleteUser"] = array('username' => $username);
	}
	
	/*	
		Function: getUsername
			Finds username to delete.
			
		Returns:
			Username given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; DeleteUser:fatih where fatih is the username.
		
		See Also:
			<Main>
	*/
	function getUsername($main){
		//Get username from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if($commands[0]=='DeleteUser' && $main->checkString('[^a-zA-Z0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Username to delete is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Username to delete is not supplied!");
			return NULL;
		}
	}
	
	/*	
		Function: delete
			Deletes given user from the site.
		
		Parameters:
			$main	   - Alumni-Online's main class instance
			$username  - Id of the user to delete
			
		Returns:
			True if delete succeed; false otherwise.
	*/
	function delete($main, $username){
		$recordSet = $main->databaseConnection->Execute("DELETE FROM {$main->databaseTablePrefix}users WHERE username='$username'");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to delete user\nreason is : ".$main->databaseConnection->ErrorMsg());
			return false;
		}
			
		return true;
	}
}
?>