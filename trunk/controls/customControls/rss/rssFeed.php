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

require_once(DIR_CONTRIB."class.RSS.php");

/*
	Class: rss
		Displays Rss feed.
	
	See Also:
		<ControlBase>
*/
class rssFeed extends ControlBase{

	/*
   		Function: actionPerform
			Displays site map on a tree.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='feed_URL' AND module_id={$moduleID}";

		$recordSet = $skin->main->databaseConnection->Execute($query);
				
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			//Assign codeBehind variables
			$skin->main->controlVariables["rssFeed"]["allItems"] = array();
			$skin->main->controlVariables["rssFeed"]["itemCount"] = 0;
			
			trigger_error("Unable to get rss feed url\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
					
			if(sizeof($rows)==1){
				$feedURL = $rows[0]['setting_value'];
				$rss = new RSS (implode("",file($feedURL)));
		
				//Assign codeBehind variables
				$allItems = $rss->getAllItems();
				$skin->main->controlVariables["rssFeed"]["allItems"] = $allItems;
				$skin->main->controlVariables["rssFeed"]["itemCount"] = count($allItems);
			}else{
				//Assign codeBehind variables
				$skin->main->controlVariables["rssFeed"]["allItems"] = array();
				$skin->main->controlVariables["rssFeed"]["itemCount"] = 0;
			}
		}
		
	}
}
?>