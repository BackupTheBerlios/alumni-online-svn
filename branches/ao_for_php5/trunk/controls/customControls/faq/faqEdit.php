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
	Class: faqEdit
	
	See Also:
		<ControlBase>
*/
class faqEdit extends ControlBase{

	/*
   		Function: actionPerform
			Displays List of questions and answers.
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$skin->main->controlVariables["faqEdit"]['moduleID'] = $moduleID;
		$skin->main->controlVariables["faqEdit"]['error']    = false;
		$skin->main->controlVariables["faqEdit"]['success']  = false;		
		
		if(isset($_POST["event"]) && $_POST["event"]=='faqEdit'){
			$queryString = "INSERT INTO {$skin->main->databaseTablePrefix}faqs (module_id, question, answer) VALUES ({$moduleID}, ".$skin->main->databaseConnection->qstr($_POST['question']).", ".$skin->main->databaseConnection->qstr($_POST['answer']).")";

			$recordSet = $skin->main->databaseConnection->Execute($queryString);
			
			if (!$recordSet) {
				$skin->main->controlVariables["faqEdit"]['error'] = true;
				trigger_error("Unable to add faq\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			}else{
				$skin->main->controlVariables["faqEdit"]['success']  = true;
			}
		}
	}
}
?>