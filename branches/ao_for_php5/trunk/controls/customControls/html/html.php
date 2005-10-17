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
	Class: html
		Displays user defined html content. Extends ControlBase
	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class html extends ControlBase{

	/*
   		Function: actionPerform
			Reads html content from database and displays.
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
			
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$content = '';
		$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='html_content' AND module_id={$moduleID}";

		$recordSet = $skin->main->databaseConnection->Execute($query);
				
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get html content information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			$displayForm = true;
		}else{		
			$rows = $recordSet->GetRows();
					
			if(sizeof($rows)==1){
				$content = $rows[0]['setting_value'];
			}
		}
		
		//Assign codeBehind variables
		$skin->main->controlVariables['html'] = array(
													'content'  => $content);		
	}
}
?>
