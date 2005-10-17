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
	Class: rssEdit
	
	See Also:
		<ControlBase>
*/
class rssEdit extends ControlBase{

	/*
   		Function: actionPerform
			Displays site map on a tree.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$skin->main->controlVariables["rssEdit"]['moduleId'] = $moduleID;
		$skin->main->controlVariables["rssEdit"]['feedUrl']  = "";
		
		if(isset($_POST["event"]) && $_POST["event"]=='rssEdit'){
			$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='feed_URL' AND module_id={$moduleID}";
	
			$recordSet = $skin->main->databaseConnection->Execute($query);
					
			//Check for error, if an error occured then report that error
			if (!$recordSet) {
				trigger_error("Unable to get feed url information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			}else{
				$rows = $recordSet->GetRows();
						
				if(sizeof($rows)==1){
					$queryString = "UPDATE {$skin->main->databaseTablePrefix}module_settings SET setting_value = \"".addslashes($_POST['feedUrl'])."\" WHERE setting_key='feed_URL' AND module_id={$moduleID}";
					//Such a entry exists, so execute an update query
					$recordSet2 =$skin->main->databaseConnection->Execute($queryString);

					//Check for error, if an error occured then report that error
					if (!$recordSet2) {
						trigger_error("Unable to update feed url information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
					}
				}else{
					$queryString = "INSERT INTO {$skin->main->databaseTablePrefix}module_settings (module_id, setting_key, setting_value) VALUES ({$moduleID}, \"feed_URL\", \"".addslashes($_POST['feedUrl'])."\")";
					//Does not exists such a value, so execute an insert query
					$recordSet3 = $skin->main->databaseConnection->Execute($queryString);
					
					//Check for error, if an error occured then report that error
					if (!$recordSet3) {
						trigger_error("Unable to add feed url information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
					}
				}
			}		
		}
		
		$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='feed_URL' AND module_id={$moduleID}";
	
		$recordSet = $skin->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get feed url information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{
			$rows = $recordSet->GetRows();
						
			if(sizeof($rows)==1){
				$skin->main->controlVariables["rssEdit"]['feedUrl']  = $rows[0]["setting_value"];
			}
		}
	}
}
?>