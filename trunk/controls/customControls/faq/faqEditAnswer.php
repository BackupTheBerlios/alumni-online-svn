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
	Class: faqEditAnswer
	
	See Also:
		<ControlBase>
*/
class faqEditAnswer extends ControlBase{

	/*
   		Function: actionPerform
			
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$faqID = $this->getFaqID($skin->main);
		$skin->main->controlVariables["faqEditAnswer"]['faqID']    = $faqID;
		$skin->main->controlVariables["faqEditAnswer"]['error']    = false;
		$skin->main->controlVariables["faqEditAnswer"]['success']  = false;
		$skin->main->controlVariables["faqEditAnswer"]['question'] = "";
		$skin->main->controlVariables["faqEditAnswer"]['answer']   = "";
		
		$queryString = "SELECT * FROM {$skin->main->databaseTablePrefix}faqs WHERE faq_id={$faqID}";
		$recordSet = $skin->main->databaseConnection->Execute($queryString);
		
		if(isset($_POST["event"]) && $_POST["event"]=='faqEditAnswer' && !$recordSet){
			$record = array();
			$record["question"] = $_POST["question"];
			$record["answer"]   = $_POST["answer"];
			
			$updateSQL = $skin->main->databaseConnection->GetUpdateSQL($recordSet, $record);
			$recordSet = $skin->main->databaseConnection->Execute($updateSQL); 
		}
		
		
		
		if (!$recordSet) {
			$skin->main->controlVariables["faqEditAnswer"]['error'] = true;
			trigger_error("Unable to get faq information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{
			if($skin->main->databaseConnection->Affected_Rows()==1){
				$skin->main->controlVariables["faqEditAnswer"]['question'] = $recordSet->Fields("question");
				$skin->main->controlVariables["faqEditAnswer"]['answer']   = $recordSet->Fields("answer");
			}
		}
	}
	
	/*	
		Function: getFaqID
			Finds faq id to edit.
			
		Returns:
			FaqID given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; EditFAQAnswer:1 where 1 is the faq id.

	*/
	function getFaqID($main){
		//Get module id from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if($commands[0]=="EditFAQAnswer" && $main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Module id to edit is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Module id to edit is not supplied!");
			return NULL;
		}
	}
}
?>