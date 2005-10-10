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
	Class: login
		Displays login forms and handles login operation. Extends ControlBase
	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class login extends ControlBase{
	/*
   		Function: actionPerform
			Performs login operation. Checks user authentication
		and sets session variable if authentication verified. Sets 
		selected tab to initial tab, recalculates user group
		and revalidates output.
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$displayForm = false;
		$usernameError = '';
		$passwordError = '';
			
		if(isset($_POST["event"]) && $_POST["event"]=='login'){
			//Check username and password.
			//Inorder to avoid sql injection attacks both 
			//should contains characters form a to z and/or numbers only			
			if(isset($_POST["username"]) && !$skin->main->checkString('[^a-zA-Z0-9]', $_POST["username"])){
				$usernameError = "Username must contains numbers and/or character from a to z only";
				$displayForm = true;
			}
			
			if(isset($_POST["passwd"]) && !$skin->main->checkString('[^a-zA-Z0-9]', $_POST["passwd"])){
				$passwordError = "Password must contains numbers and/or character from a to z only";
				$displayForm = true;
			}
			
			if(isset($_POST["passwd"]) && $_POST["passwd"]==''){
				$passwordError = "Password can not be empty!";
				$displayForm = true;
			}
			
			if(isset($_POST["username"]) && $_POST["username"]==''){
				$passwordError = "Username can not be empty!";
				$displayForm = true;
			}
			
		    if(!$displayForm){
				$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}users WHERE is_active=1 AND username=".$skin->main->databaseConnection->qstr($_POST["username"])." AND password='".md5($_POST["passwd"])."'";
				$recordSet = $skin->main->databaseConnection->Execute($query);
				
				//Check for error, if an error occured then report that error
				if (!$recordSet) {
					trigger_error("Unable to check user authentication\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
					$displayForm = true;
				}else{		
					if($skin->main->databaseConnection->Affected_Rows()==1){
						session_register('username');
						$_SESSION["username"] = $_POST["username"];
						
						$skin->main->eventHandler->fireEvent("login_succeed", $_POST["username"]);
						$skin->main->selectedTab = $skin->main->getInitialTab();
						$skin->main->userGroup   = $skin->main->getUserGroup();
						$skin->main->revalidate  = TRUE;
					}else{
						$skin->main->eventHandler->fireEvent("login_fail", $_POST["username"]);
						$displayForm = true;
					}
				}
			}
		}else{
			$displayForm = true;
		}
		
		//Assign codeBehind variables
		$skin->main->controlVariables["login"] = array(
													'displayForm'  => $displayForm,
													'usernameError' => $usernameError,
													'passwordError' => $passwordError);				

	}
}
?>