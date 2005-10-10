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

require_once(DIR_CONTRIB.'ICQ_Status.class.php');

/*
	Class: icqStatus
		Displays icq status of given user.
	
	See Also:
		<ControlBase>
*/
class icqStatus extends ControlBase{

	/*
   		Function: actionPerform
			Displays site map on a tree.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='icq_number' AND module_id={$moduleID}";

		$recordSet = $skin->main->databaseConnection->Execute($query);
				
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get icq number\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			$skin->main->controlVariables["icqStatus"]["status"] = "Unknown";
		}else{		
			$rows = $recordSet->GetRows();
					
			if(sizeof($rows)==1){
				$icqId = $rows[0]['setting_value'];
				$icq = new ICQ_Status();
		
				//Assign codeBehind variables
				$skin->main->controlVariables["icqStatus"]["status"] = $icq->GetStatus($icqId);
			}else{
				$skin->main->controlVariables["icqStatus"]["status"] = "Unknown";
			}
		}
		
		$skin->main->controlVariables["icqStatus"]["theme"]  = $skin->main->applicationSettings['theme'];
	}
}
?>