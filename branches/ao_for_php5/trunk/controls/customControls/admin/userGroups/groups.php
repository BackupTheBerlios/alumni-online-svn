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
Class: groups
	Displays roles list. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class groups extends ControlBase{	
	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. 
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
	*/
	function actionPerform(&$skin, $moduleID){
		$recordSet = $skin->main->databaseConnection->Execute("SELECT user_groups.* , COUNT(users.name) AS user_count 
																FROM
																	{$skin->main->databaseTablePrefix}user_groups AS user_groups LEFT OUTER JOIN
																	{$skin->main->databaseTablePrefix}users AS users
																ON
																	user_groups.user_group_id = users.user_group_id
																WHERE
																	user_groups.user_group_id>1
																GROUP BY
																	user_groups.user_group_id");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user group list\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			$skin->main->controlVariables["groups"]['groupList']  = $rows;
			$skin->main->controlVariables["groups"]['groupCount'] = sizeof($rows);
		}
	}
}
?>