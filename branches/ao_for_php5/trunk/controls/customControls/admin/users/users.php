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
Class: users
	Displays user list. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class users extends ControlBase{	
	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. 
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
	*/
	function actionPerform(&$skin, $moduleID){
		$recordSet = $skin->main->databaseConnection->Execute("SELECT 
																users.*,  user_groups.name AS group_name
															 FROM {$skin->main->databaseTablePrefix}users AS users 
															 	LEFT OUTER  JOIN {$skin->main->databaseTablePrefix}user_groups AS user_groups 
																ON users.user_group_id = user_groups.user_group_id");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user list\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			$skin->main->controlVariables["users"]['userList']  = $rows;
			$skin->main->controlVariables["users"]['userCount'] = sizeof($rows);
		}
	}
}
?>