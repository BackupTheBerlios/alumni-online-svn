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
	Class: pollEditAnswers
		Edits poll's answers.
	
	See Also:
		<ControlBase>
*/
class pollEditAnswers extends ControlBase{

	/*
		Object: $main
			Main class instance
	*/
	var $main;
	
	/*
		Object: $moduleID
			Id of current module
	*/
	var $moduleID;

	/*
   		Function: actionPerform
			Displays/Edits poll
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$this->main     = $skin->main;
		$this->moduleID = $moduleID;
		
		//Assign codeBehind variables
		$this->main->controlVariables["pollEditAnswers"]["moduleId"] = $moduleID;
		$this->main->controlVariables["pollEditAnswers"]["answers"]  = array();
		
		if(isset($_POST["event"]) && $_POST["event"]=="pollEditAnswers_{$moduleID}" && isset($_POST["answer"])){
			$this->deleteAnswer();
		}
		
		$this->setPollAnswers();
	}
	
	/*
   		Function: deleteAnswer
			Deletes selected answer.
   */
	function deleteAnswer(){
		foreach ($_POST["answer"] as $answerId){
			$query =  "DELETE FROM {$this->main->databaseTablePrefix}poll_answers WHERE poll_answers_id ={$answerId}";
	
			$recordSet = $this->main->databaseConnection->Execute($query);
						
			//Check for error, if an error occured then report that error
			if (!$recordSet) {
				trigger_error("Unable to delete answer '{$answerId}'\nreason is : ".$this->main->databaseConnection->ErrorMsg());
			}
		}
	}
	
	/*
   		Function: setPollAnswers
			Binds poll answers.
   */
	function setPollAnswers(){
		$query =  "SELECT  *  FROM {$this->main->databaseTablePrefix}poll_questions WHERE poll_module_id ={$this->moduleID}";

		$recordSet = $this->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if ($recordSet) {
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){				
				$query2 =  "SELECT  *  FROM {$this->main->databaseTablePrefix}poll_answers WHERE poll_module_id ={$this->moduleID}";

				$recordSet2 = $this->main->databaseConnection->Execute($query2);
				$this->main->controlVariables["pollEditAnswers"]["answers"] = $recordSet2->GetRows();
			}
		}
	}
}
?>