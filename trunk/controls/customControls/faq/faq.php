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
	Class: faq
	
	See Also:
		<ControlBase>
*/
class faq extends ControlBase{

	/*
   		Function: actionPerform
			Displays List of questions and answers.
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}faqs WHERE module_id={$moduleID}";
	
		$recordSet = $skin->main->databaseConnection->Execute($query);
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			$skin->main->controlVariables["faq"]['faqList'] = array();
			trigger_error("Unable to get faq list\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{
			$rows = $recordSet->GetRows();
			$skin->main->controlVariables["faq"]['faqList'] = $rows;
		}
		
		$skin->main->controlVariables["faq"]['canUserEdit'] = $this->canUserEditModule($skin->main, $moduleID);
	}
	
	/*
		Function: canUserEditModule
			Indicates whether user authorized to edit module.
				
			$main     - Main class of Alumni-Online
			$moduleID - Module id to check authentication
				
		Returns:
			True if user authorized to edit module, false otherwise.
	*/
	function canUserEditModule($main, $moduleID){	
		$recordSet = $main->databaseConnection->Execute("SELECT pane_name FROM {$main->databaseTablePrefix}modules WHERE module_id = $moduleID AND (administrator_roles LIKE '%;".$main->userGroup.";%' OR administrator_roles LIKE '%;1;%')");
	
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to check whether user can edit\nreason is : ".$main->databaseConnection->ErrorMsg());
			return FALSE;
		}
		
		if($main->databaseConnection->Affected_Rows()==1){
			return TRUE;
		}
		
		return FALSE;
	}
}
?>