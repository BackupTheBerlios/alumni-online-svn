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

require_once(DIR_CONTRIB.'CYahooStatus.php');

/*
	Class: yahootatus
		Displays yahoo status of given user.
	
	See Also:
		<ControlBase>
*/
class yahooStatus extends ControlBase{

	/*
   		Function: actionPerform
			Displays site map on a tree.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
	
	$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='yahooId' AND module_id={$moduleID}";

		$recordSet = $skin->main->databaseConnection->Execute($query);
				
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get yahoo number\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			$skin->main->controlVariables["yahooStatus"]["status"] = "Unknown";
		}else{		
			$rows = $recordSet->GetRows();
					
			if(sizeof($rows)==1){
				$yahooId = $rows[0]['setting_value'];
				
				$yahoostatus = new CYahooStatus ();
				$status = $yahoostatus->execute ($yahooId, $errno, $errstr);
				if ($status !== false)
				{
					switch ($status) { 
						case YAHOO_ONLINE: 
							$skin->main->controlVariables["yahooStatus"]["status"] = "online";
							break; 
						case YAHOO_OFFLINE: 
							$skin->main->controlVariables["yahooStatus"]["status"] = "offline";
							break; 
						case YAHOO_UNKNOWN: 
							$skin->main->controlVariables["yahooStatus"]["status"] = "unknown";
							break; 
					} 		
				}
				else
				{
					trigger_error("An error occurred during Yahoo! Status query\nreason is => Error no '$errno', message '$errstr'");
					$skin->main->controlVariables["yahooStatus"]["status"] = "unknown";
				}
			}else{
				$skin->main->controlVariables["yahooStatus"]["status"] = "Unknown";
			}
			
			$skin->main->controlVariables["yahooStatus"]["theme"]  = $skin->main->applicationSettings['theme'];
		}
	}
}
?>